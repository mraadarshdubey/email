<?php

namespace App\Http\Controllers;

use App\Models\EmailTracking;
use Illuminate\Support\Facades\DB;

class BroadcastController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $rows = EmailTracking::where('user_id', auth()->id())
            ->selectRaw("COALESCE(batch_id, 'single-' || id) as group_key")
            ->selectRaw('subject')
            ->selectRaw('MIN(sent_at) as sent_at')
            ->selectRaw('COUNT(*) as recipients')
            ->selectRaw('SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened')
            ->selectRaw('SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked')
            ->groupBy('group_key', 'subject')
            ->orderByDesc('sent_at')
            ->get()
            ->map(function ($row) {
                $row->open_rate = $row->recipients > 0 ? round(($row->opened / $row->recipients) * 100, 1) : 0;
                $row->click_rate = $row->recipients > 0 ? round(($row->clicked / $row->recipients) * 100, 1) : 0;
                return $row;
            });

        return view('broadcasts.index', ['broadcasts' => $rows]);
    }
}
