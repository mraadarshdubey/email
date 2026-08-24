@extends('layouts.app')

@section('title', 'RSS Feeds - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">RSS Feeds</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">RSS Feeds</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Automatically email subscribers a digest when a feed publishes something new</p>
    </div>
    <a href="{{ route('rss-feeds.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>Add Feed
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($feeds->isEmpty())
    <div class="card">
        <div class="card-body text-center" style="padding:48px 20px;color:#94a3b8;">
            <i class="bi bi-rss" style="font-size:32px;"></i>
            <p style="margin:12px 0 16px;font-size:13px;">No feeds yet. Add an RSS/Atom feed URL to auto-generate digest emails.</p>
            <a href="{{ route('rss-feeds.create') }}" class="btn btn-primary btn-sm">Add Your First Feed</a>
        </div>
    </div>
@else
<div class="card">
    <div class="card-body" style="padding:0;">
        <div class="table-responsive">
            <table class="table data-table">
                <thead>
                    <tr>
                        <th>Feed</th>
                        <th>Recipients (tag)</th>
                        <th>Last Checked</th>
                        <th>Sent</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($feeds as $feed)
                    <tr>
                        <td>
                            <strong>{{ $feed->name }}</strong><br>
                            <span style="font-size:11px;color:#94a3b8;">{{ \Illuminate\Support\Str::limit($feed->feed_url, 50) }}</span>
                        </td>
                        <td>{{ $feed->recipientTag->name ?? '—' }}</td>
                        <td>{{ $feed->last_checked_at?->diffForHumans() ?? 'Never' }}</td>
                        <td>{{ $feed->sent_count }}</td>
                        <td><span class="badge {{ $feed->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $feed->is_active ? 'Active' : 'Paused' }}</span></td>
                        <td>
                            <div class="d-flex gap-1">
                                <form action="{{ route('rss-feeds.check-now', $feed) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Check now"><i class="bi bi-arrow-repeat"></i></button>
                                </form>
                                <form action="{{ route('rss-feeds.toggle-active', $feed) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ $feed->is_active ? 'Pause' : 'Activate' }}">
                                        <i class="bi {{ $feed->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                                    </button>
                                </form>
                                <a href="{{ route('rss-feeds.edit', $feed) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                                <form action="{{ route('rss-feeds.destroy', $feed) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this feed?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Remove"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<p style="font-size:12px;color:#94a3b8;margin-top:12px;">
    <i class="bi bi-info-circle"></i>
    First check on a new feed never sends anything — it just marks the newest post as the starting point, so subscribers don't get the entire archive at once.
</p>
@endsection
