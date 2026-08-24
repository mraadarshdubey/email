@extends('layouts.app')

@section('title', 'Email Templates - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Email Templates</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Email Templates</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $stats['total'] }} templates &middot; {{ $stats['active'] }} active &middot; {{ number_format($stats['total_usage']) }} uses</p>
    </div>
    <a href="{{ route('email-templates.create') }}" class="btn btn-primary btn-sm">Create Template</a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-3">
    @forelse($templates as $template)
    <div class="col-sm-6 col-lg-4 col-xl-3">
        <div class="card h-100" style="overflow:hidden;">
            <a href="{{ route('email-templates.show', $template) }}" style="text-decoration:none;color:inherit;">
                @include('partials._template-thumb', ['template' => $template])
            </a>
            <div class="card-body" style="padding:14px 16px;">
                <div class="d-flex justify-content-between align-items-start mb-1">
                    <h6 style="font-size:13px;font-weight:600;color:#0f172a;margin:0;">{{ $template->name }}</h6>
                    <span class="badge {{ $template->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size:9px;flex-shrink:0;margin-left:6px;">{{ $template->is_active ? 'Active' : 'Off' }}</span>
                </div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $template->subject }}</div>
                <div class="d-flex gap-1 flex-wrap">
                    <a href="{{ route('email-templates.edit', $template) }}" class="btn btn-sm btn-outline-primary" style="font-size:11px;padding:4px 8px;">Edit</a>
                    <form action="{{ route('email-templates.duplicate', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-primary" style="font-size:11px;padding:4px 8px;">Copy</button>
                    </form>
                    <form action="{{ route('email-templates.toggle-active', $template) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-primary" style="font-size:11px;padding:4px 8px;">{{ $template->is_active ? 'Disable' : 'Enable' }}</button>
                    </form>
                    <form action="{{ route('email-templates.destroy', $template) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger" style="font-size:11px;padding:4px 8px;" onclick="return confirm('Delete this template?')"><i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center py-5">
                <p style="font-size:14px;color:#94a3b8;margin-bottom:16px;">No templates yet.</p>
                <a href="{{ route('email-templates.create') }}" class="btn btn-primary">Create Your First Template</a>
            </div>
        </div>
    </div>
    @endforelse
</div>
@endsection
