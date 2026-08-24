@extends('layouts.app')

@section('title', 'Automation Rules - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Rules</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Rules</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">"If this, then that" automations for your contacts</p>
    </div>
    <a href="{{ route('automation-rules.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Rule
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($rules->isEmpty())
    <div class="card">
        <div class="card-body text-center" style="padding:48px 20px;color:#94a3b8;">
            <i class="bi bi-shuffle" style="font-size:32px;"></i>
            <p style="margin:12px 0 16px;font-size:13px;">No rules yet. Create one to automatically email contacts when something happens.</p>
            <a href="{{ route('automation-rules.create') }}" class="btn btn-primary btn-sm">Create Your First Rule</a>
        </div>
    </div>
@else
<div class="row g-3">
    @foreach($rules as $rule)
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 style="font-size:14px;font-weight:600;color:#0f172a;margin:0;">{{ $rule->name }}</h6>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $rule->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size:10px;">{{ $rule->is_active ? 'Active' : 'Paused' }}</span>
                    </div>
                </div>

                <div class="d-flex align-items-center" style="font-size:13px;color:#334155;gap:8px;margin-bottom:8px;">
                    <span class="badge" style="background:#f1f5f9;color:#0f172a;font-weight:500;">IF</span>
                    <span>{{ $rule->triggerLabel() }}</span>
                </div>
                <div class="d-flex align-items-center" style="font-size:13px;color:#334155;gap:8px;margin-bottom:16px;">
                    <span class="badge" style="background:#f1f5f9;color:#0f172a;font-weight:500;">THEN</span>
                    <span>{{ $rule->actionLabel() }}</span>
                </div>

                <div class="d-flex justify-content-between align-items-center" style="font-size:11px;color:#94a3b8;border-top:1px solid #f1f5f9;padding-top:12px;">
                    <span>Ran {{ $rule->runs_count }}x{{ $rule->last_run_at ? ' · last ' . $rule->last_run_at->diffForHumans() : '' }}</span>
                    <div class="d-flex gap-1">
                        <form action="{{ route('automation-rules.toggle-active', $rule) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ $rule->is_active ? 'Pause' : 'Activate' }}">
                                <i class="bi {{ $rule->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                            </button>
                        </form>
                        <a href="{{ route('automation-rules.edit', $rule) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('automation-rules.destroy', $rule) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this rule?')">
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
