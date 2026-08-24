<?php

namespace App\Http\Controllers;

use App\Models\EmailTracking;
use Illuminate\Http\Request;

class EmailActivityController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = EmailTracking::where('user_id', auth()->id())->with('clicks');

        $batch = $request->query('batch');
        if ($batch) {
            if (str_starts_with($batch, 'single-')) {
                // Synthetic group key for legacy rows sent before batch_id existed.
                $query->whereNull('batch_id')->where('id', (int) substr($batch, 7));
            } else {
                $query->where('batch_id', $batch);
            }
        }

        $trackedEmails = $query->orderByDesc('sent_at')->get();

        $stats = [
            'total' => $trackedEmails->count(),
            'opened' => $trackedEmails->whereNotNull('opened_at')->count(),
            'clicked' => $trackedEmails->whereNotNull('clicked_at')->count(),
        ];

        return view('email-activity.index', compact('trackedEmails', 'stats', 'batch'));
    }
}
