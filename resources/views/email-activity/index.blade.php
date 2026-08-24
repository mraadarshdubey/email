@extends('layouts.app')

@section('title', 'Email Activity - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Email Activity</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Email Activity</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Open &amp; click tracking for individually sent emails</p>
    </div>
    <button type="button" class="btn btn-outline-primary btn-sm" onclick="location.reload()">
        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
    </button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body" style="padding:18px 20px;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Emails Sent</div>
                <div style="font-size:26px;font-weight:700;color:#0f172a;margin-top:4px;">{{ $stats['total'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body" style="padding:18px 20px;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Opened</div>
                <div style="font-size:26px;font-weight:700;color:#16a34a;margin-top:4px;">{{ $stats['opened'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body" style="padding:18px 20px;">
                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.05em;color:#64748b;">Clicked</div>
                <div style="font-size:26px;font-weight:700;color:#2563eb;margin-top:4px;">{{ $stats['clicked'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding:0;">
        @if($trackedEmails->isEmpty())
            <div class="text-center" style="padding:48px 20px;color:#94a3b8;">
                <i class="bi bi-envelope-open" style="font-size:32px;"></i>
                <p style="margin:12px 0 0;font-size:13px;">No tracked emails yet. Send an individual email to see open &amp; click activity here.</p>
            </div>
        @else
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Recipient</th>
                        <th>Subject</th>
                        <th>Sent</th>
                        <th>Opened</th>
                        <th>Clicked</th>
                        <th>Links Clicked</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trackedEmails as $item)
                    <tr>
                        <td><strong>{{ $item->recipient }}</strong></td>
                        <td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->subject }}</td>
                        <td>{{ $item->sent_at?->format('d M Y, h:i A') }}</td>
                        <td>
                            @if($item->isOpened())
                                <span class="badge bg-success"><i class="bi bi-eye"></i> Opened</span>
                                <div style="font-size:11px;color:#64748b;margin-top:3px;">
                                    {{ $item->opened_at->format('d M, h:i A') }} &middot; {{ $item->open_count }}x
                                </div>
                                @if($item->last_open_ip)
                                <div style="font-size:10px;color:#94a3b8;margin-top:2px;" title="{{ $item->last_open_user_agent }}">
                                    IP {{ $item->last_open_ip }}
                                </div>
                                @endif
                            @else
                                <span class="badge" style="background:#e2e8f0;color:#64748b;">Not opened</span>
                            @endif
                        </td>
                        <td>
                            @if($item->isClicked())
                                <span class="badge bg-primary"><i class="bi bi-cursor"></i> Clicked</span>
                                <div style="font-size:11px;color:#64748b;margin-top:3px;">
                                    {{ $item->clicked_at->format('d M, h:i A') }} &middot; {{ $item->click_count }}x
                                </div>
                            @else
                                <span class="badge" style="background:#e2e8f0;color:#64748b;">No clicks</span>
                            @endif
                        </td>
                        <td style="font-size:12px;">
                            @forelse($item->clicks as $click)
                                <div class="mb-1" style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $click->url }}">
                                    <i class="bi bi-link-45deg"></i> {{ $click->url }}
                                </div>
                            @empty
                                <span style="color:#cbd5e1;">&mdash;</span>
                            @endforelse
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
