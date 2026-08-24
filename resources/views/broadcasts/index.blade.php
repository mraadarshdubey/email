@extends('layouts.app')

@section('title', 'Broadcasts - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Broadcasts</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Broadcasts</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Performance of every individual-email send, grouped as a broadcast</p>
    </div>
    <a href="{{ route('individual-emails.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Broadcast
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($broadcasts->isEmpty())
            <div class="text-center" style="padding:48px 20px;color:#94a3b8;">
                <i class="bi bi-megaphone" style="font-size:32px;"></i>
                <p style="margin:12px 0 0;font-size:13px;">No broadcasts yet. Send an individual email campaign to see performance here.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Subject Line</th>
                        <th>Date</th>
                        <th>Recipients</th>
                        <th>Opened</th>
                        <th>Open Rate</th>
                        <th>Clicked</th>
                        <th>Click Rate</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($broadcasts as $b)
                    <tr>
                        <td><strong>{{ $b->subject }}</strong></td>
                        <td style="white-space:nowrap;">{{ \Illuminate\Support\Carbon::parse($b->sent_at)->format('D g:ia') }}<br><span style="color:#94a3b8;font-size:11px;">{{ \Illuminate\Support\Carbon::parse($b->sent_at)->format('M j, Y') }}</span></td>
                        <td>{{ $b->recipients }}</td>
                        <td>{{ $b->opened }}</td>
                        <td>
                            <span class="badge" style="background:{{ $b->open_rate > 0 ? '#dcfce7' : '#f1f5f9' }};color:{{ $b->open_rate > 0 ? '#16a34a' : '#94a3b8' }};">{{ $b->open_rate }}%</span>
                        </td>
                        <td>{{ $b->clicked }}</td>
                        <td>
                            <span class="badge" style="background:{{ $b->click_rate > 0 ? '#dbeafe' : '#f1f5f9' }};color:{{ $b->click_rate > 0 ? '#2563eb' : '#94a3b8' }};">{{ $b->click_rate }}%</span>
                        </td>
                        <td>
                            <a href="{{ route('email-activity.index', ['batch' => $b->group_key]) }}" class="btn btn-outline-primary btn-sm">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<p style="font-size:12px;color:#94a3b8;margin-top:12px;">
    <i class="bi bi-info-circle"></i>
    Unsubscribe tracking and public web-archive links aren't built yet — this tool doesn't manage a suppression list.
    Let me know if you want those added too.
</p>
@endsection
