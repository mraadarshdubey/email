@extends('layouts.app')

@section('title', 'Follow-ups - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Follow-ups</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Follow-ups</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Automatically resend to whoever hasn't opened or clicked a past broadcast</p>
    </div>
    <a href="{{ route('email-followups.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Follow-up
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($followups->isEmpty())
    <div class="card">
        <div class="card-body text-center" style="padding:48px 20px;color:#64748b;">
            <i class="bi bi-reply" style="font-size:32px;"></i>
            <p style="margin:12px 0 16px;font-size:13px;">No follow-ups yet. Pick a broadcast you've already sent, and automatically nudge whoever hasn't opened or clicked.</p>
            <a href="{{ route('email-followups.create') }}" class="btn btn-primary btn-sm">Create Your First Follow-up</a>
        </div>
    </div>
@else
<div class="row g-3">
    @foreach($followups as $f)
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 style="font-size:14px;font-weight:600;margin:0;">{{ $f->name }}</h6>
                    <span class="badge {{ $f->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $f->is_active ? 'Active' : 'Paused' }}</span>
                </div>

                <div style="font-size:12px;color:#64748b;margin-bottom:4px;">Original broadcast</div>
                <div style="font-size:13px;font-weight:500;margin-bottom:12px;">{{ $f->source_subject ?? '—' }}</div>

                <div class="d-flex align-items-center" style="font-size:13px;gap:8px;margin-bottom:8px;">
                    <span class="badge" style="background:rgba(139,92,246,0.1);color:#a78bfa;">IF</span>
                    <span>{{ $f->conditionLabel() }} within {{ $f->waitLabel() }}</span>
                </div>
                <div class="d-flex align-items-center" style="font-size:13px;gap:8px;margin-bottom:16px;">
                    <span class="badge" style="background:rgba(139,92,246,0.1);color:#a78bfa;">THEN</span>
                    <span>Send "{{ $f->emailTemplate->name ?? '—' }}"</span>
                </div>

                <div class="d-flex justify-content-between align-items-center" style="font-size:11px;color:#64748b;border-top:1px solid rgba(255,255,255,0.08);padding-top:12px;">
                    <span>{{ $f->pending_count }} pending &middot; {{ $f->sent_count }} sent{{ $f->last_run_at ? ' · last checked ' . $f->last_run_at->diffForHumans() : '' }}</span>
                    <div class="d-flex gap-1">
                        <form action="{{ route('email-followups.run-now', $f) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Run now"><i class="bi bi-lightning-charge"></i></button>
                        </form>
                        <form action="{{ route('email-followups.toggle-active', $f) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ $f->is_active ? 'Pause' : 'Activate' }}">
                                <i class="bi {{ $f->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                            </button>
                        </form>
                        <a href="{{ route('email-followups.edit', $f) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('email-followups.destroy', $f) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this follow-up?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete"><i class="bi bi-trash"></i></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
