<?php

namespace App\Http\Controllers;

use App\Models\EmailAccount;
use App\Models\EmailFollowup;
use App\Models\EmailTemplate;
use App\Models\EmailTracking;
use App\Services\FollowupRunnerService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class EmailFollowupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $followups = EmailFollowup::where('user_id', Auth::id())
            ->with('emailTemplate')
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($f) {
                $f->pending_count = EmailTracking::forBatchKey($f->source_batch_id)
                    ->whereNull('followed_up_at')
                    ->when($f->condition === 'not_opened', fn ($q) => $q->whereNull('opened_at'), fn ($q) => $q->whereNull('clicked_at'))
                    ->count();
                return $f;
            });

        return view('email-followups.index', compact('followups'));
    }

    public function create()
    {
        return view('email-followups.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validateFollowup($request);
        $data['user_id'] = Auth::id();

        EmailFollowup::create($data);

        return redirect()->route('email-followups.index')->with('success', 'Follow-up created.');
    }

    public function edit(EmailFollowup $email_followup)
    {
        $this->authorizeOwner($email_followup);

        return view('email-followups.edit', array_merge(
            $this->formData(),
            ['followup' => $email_followup]
        ));
    }

    public function update(Request $request, EmailFollowup $email_followup)
    {
        $this->authorizeOwner($email_followup);
        $data = $this->validateFollowup($request);
        $email_followup->update($data);

        return redirect()->route('email-followups.index')->with('success', 'Follow-up updated.');
    }

    public function destroy(EmailFollowup $email_followup)
    {
        $this->authorizeOwner($email_followup);
        $email_followup->delete();

        return back()->with('success', 'Follow-up removed.');
    }

    public function toggleActive(EmailFollowup $email_followup)
    {
        $this->authorizeOwner($email_followup);
        $email_followup->update(['is_active' => !$email_followup->is_active]);

        return back()->with('success', 'Follow-up ' . ($email_followup->is_active ? 'activated' : 'paused') . '.');
    }

    public function runNow(EmailFollowup $email_followup, FollowupRunnerService $runner)
    {
        $this->authorizeOwner($email_followup);
        $count = $runner->processDue($email_followup->id);

        return back()->with('success', $count > 0 ? "Sent {$count} follow-up email(s)." : 'Nobody is due for a follow-up yet.');
    }

    private function authorizeOwner(EmailFollowup $followup): void
    {
        abort_unless($followup->user_id === Auth::id(), 403);
    }

    private function formData(): array
    {
        // Group the same way Broadcasts does: real batch_id when present,
        // otherwise a synthetic per-row key so legacy single sends (from
        // before batch_id existed) still show up as pickable sources.
        $broadcasts = EmailTracking::where('user_id', Auth::id())
            ->selectRaw("COALESCE(batch_id, 'single-' || id) as batch_id")
            ->selectRaw('subject')
            ->selectRaw('MIN(sent_at) as sent_at')
            ->selectRaw('COUNT(*) as recipients')
            ->groupBy('batch_id', 'subject')
            ->orderByDesc('sent_at')
            ->get();

        return [
            'broadcasts' => $broadcasts,
            'templates' => EmailTemplate::where('is_active', true)->get(),
            'emailAccounts' => EmailAccount::where('is_active', true)->get(),
        ];
    }

    private function validateFollowup(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'source_batch_id' => 'required|string',
            'condition' => 'required|in:not_clicked,not_opened',
            'wait_value' => 'required|integer|min:1',
            'wait_unit' => 'required|in:minutes,hours,days',
            'email_template_id' => 'required|exists:email_templates,id',
            'email_account_id' => 'required|exists:email_accounts,id',
            'is_active' => 'nullable|boolean',
        ]);

        $validator->validate();

        $multiplier = match ($request->wait_unit) {
            'hours' => 60,
            'days' => 1440,
            default => 1,
        };

        $sourceSubject = EmailTracking::forBatchKey($request->source_batch_id)->value('subject');

        return [
            'name' => $request->name,
            'source_batch_id' => $request->source_batch_id,
            'source_subject' => $sourceSubject,
            'condition' => $request->condition,
            'wait_minutes' => (int) $request->wait_value * $multiplier,
            'email_template_id' => $request->email_template_id,
            'email_account_id' => $request->email_account_id,
            'is_active' => $request->has('is_active'),
        ];
    }
}
