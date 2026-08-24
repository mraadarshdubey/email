<?php

namespace App\Http\Controllers;

use App\Imports\ImportData;
use App\Jobs\DispatchCampaignJob;
use App\Jobs\SendEmailJob;
use App\Mail\SendMail;
use App\Models\OneTimeSender;
use App\Models\TempMailAddress;
use App\Models\EmailAccount;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Sleep;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Mail;
use Exception;

class InstantCampaignController extends Controller
{
    /**
     * Show the instant campaign creation form
     */
    public function create(Request $request)
    {
        $templates = EmailTemplate::active()->orderBy('name')->get();
        $selectedTemplate = null;

        // Check if a template was selected via query parameter
        if ($request->has('template')) {
            $selectedTemplate = EmailTemplate::active()->find($request->get('template'));
        }

        return view('instant-campaign.create', compact('templates', 'selectedTemplate'));
    }

    public function import(Request $request)
    {
        try {
            // Check if there's a default email account configured
            $defaultEmailAccount = EmailAccount::getDefault();

            if (!$defaultEmailAccount) {
                return back()->with('error', 'No default email account configured. Please add and configure an email account first.');
            }

            // Validate the request with custom messages
            $request->validate([
                'file' => 'required|file|mimetypes:application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,text/csv|max:10240',
                'subject' => 'required|string|max:255',
                'body' => 'required|string|max:65535',
                'template_id' => 'nullable|exists:email_templates,id',
            ], [
                'file.required' => 'Please upload an Excel or CSV file.',
                'file.file' => 'The uploaded file is not valid.',
                'file.mimetypes' => 'Please upload a valid Excel (.xlsx, .xls) or CSV (.csv) file.',
                'file.max' => 'The file size must not exceed 10MB.',
                'subject.required' => 'Please enter an email subject.',
                'subject.max' => 'Subject must not exceed 255 characters.',
                'body.required' => 'Please enter the email content.',
                'body.max' => 'Email content is too long. Please reduce the content size.',
                'template_id.exists' => 'Selected template not found.',
            ]);

            // Clear only orphaned rows (never assigned to a campaign). Rows that
            // belong to a campaign are owned by its background dispatcher and
            // must not be wiped here.
            TempMailAddress::whereNull('campaign_id')->delete();

            // Track template usage if a template was used
            $usedTemplate = null;
            if ($request->filled('template_id')) {
                $usedTemplate = EmailTemplate::find($request->template_id);
                if ($usedTemplate) {
                    $usedTemplate->incrementUsage();
                }
            }

            // Start database transaction
            DB::beginTransaction();

            try {
                // Import the Excel file
                Excel::import(new ImportData, $request->file('file'));

                $emailCount = TempMailAddress::count();

                if ($emailCount === 0) {
                    DB::rollBack();
                    return back()->with('error', 'No valid email addresses found in the uploaded file. Please ensure email addresses are in the 3rd column (Column C).');
                }

                // Check for reasonable limits (prevent abuse)
                $maxCampaignSize = (int) config('bulkmail.max_campaign_size', 100000);
                if ($emailCount > $maxCampaignSize) {
                    DB::rollBack();
                    return back()->with('error', 'Too many email addresses (' . number_format($emailCount) . '). Maximum allowed is ' . number_format($maxCampaignSize) . ' per campaign.');
                }

                // Create campaign record
                $campaign = OneTimeSender::create([
                    'file_name' => $request->file->getClientOriginalName(),
                    'total_email_address' => $emailCount,
                    'subject' => $request->subject,
                    'status' => 'processing',
                    'created_at' => now(),
                ]);

                $mailData = [
                    'subject' => $request->subject,
                    'body' => $request->body,
                    'email_account_id' => $defaultEmailAccount->id,
                    'campaign_id' => $campaign->id,
                ];

                // Tag this import's recipients with the campaign, then hand the
                // heavy fan-out to a background job. This keeps the request fast
                // regardless of list size (works the same for 100 or 70,000).
                TempMailAddress::whereNull('campaign_id')->update(['campaign_id' => $campaign->id]);

                Log::info("Starting instant campaign", [
                    'campaign_id' => $campaign->id,
                    'total_emails' => $emailCount,
                    'email_account' => $defaultEmailAccount->name,
                ]);

                DispatchCampaignJob::dispatch($campaign->id, $mailData)->onQueue('default');

                // Mark last-used now; per-email counters are incremented by each
                // SendEmailJob as it actually sends (no bulk pre-increment, which
                // previously double-counted).
                $defaultEmailAccount->update(['last_used_at' => now()]);

                DB::commit();

                return back()->with([
                    'message' => 'Success! Campaign for ' . number_format($emailCount) . ' recipients is being queued for delivery using "' . $defaultEmailAccount->name . '". Large lists dispatch in the background.',
                ]);

            } catch (Exception $e) {
                DB::rollBack();
                Log::error("Failed to process instant campaign", [
                    'error' => $e->getMessage(),
                    'file' => $request->file->getClientOriginalName(),
                    'user_id' => auth()->id()
                ]);

                return back()->with('error', 'Failed to process the campaign. Please check your file format and try again.');
            }

        } catch (Exception $e) {
            Log::error("Instant campaign validation failed", [
                'error' => $e->getMessage(),
                'user_id' => auth()->id()
            ]);

            return back()->with('error', 'An unexpected error occurred. Please try again.');
        }
    }
}
