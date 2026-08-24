@extends('layouts.app')

@section('title', 'Sequences - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Sequences</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Sequences</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Multi-step drip campaigns — tag a contact, they get a series of emails over time</p>
    </div>
    <a href="{{ route('automation-sequences.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Sequence
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($sequences->isEmpty())
    <div class="card">
        <div class="card-body text-center" style="padding:48px 20px;color:#94a3b8;">
            <i class="bi bi-signpost-split" style="font-size:32px;"></i>
            <p style="margin:12px 0 16px;font-size:13px;">No sequences yet. Build a step-by-step email series triggered by a tag.</p>
            <a href="{{ route('automation-sequences.create') }}" class="btn btn-primary btn-sm">Create Your First Sequence</a>
        </div>
    </div>
@else
<div class="row g-3">
    @foreach($sequences as $seq)
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 style="font-size:14px;font-weight:600;color:#0f172a;margin:0;">{{ $seq->name }}</h6>
                    <span class="badge {{ $seq->is_active ? 'bg-success' : 'bg-secondary' }}" style="font-size:10px;">{{ $seq->is_active ? 'Active' : 'Paused' }}</span>
                </div>

                <div class="d-flex align-items-center" style="font-size:13px;color:#334155;gap:8px;margin-bottom:12px;">
                    <span class="badge" style="background:#f1f5f9;color:#0f172a;font-weight:500;">TRIGGER</span>
                    <span>Tagged "{{ $seq->triggerTag->name ?? '—' }}"</span>
                </div>

                <div class="d-flex justify-content-between" style="font-size:12px;color:#64748b;margin-bottom:16px;">
                    <span><i class="bi bi-list-ol"></i> {{ $seq->steps_count }} step{{ $seq->steps_count != 1 ? 's' : '' }}</span>
                    <span><i class="bi bi-people"></i> {{ $seq->active_enrollments_count }} in progress</span>
                    <span><i class="bi bi-check2-circle"></i> {{ $seq->completed_enrollments_count }} completed</span>
                </div>

                <div class="d-flex justify-content-between align-items-center" style="border-top:1px solid #f1f5f9;padding-top:12px;">
                    <a href="{{ route('automation-sequences.show', $seq) }}" class="btn btn-sm btn-outline-primary">View Enrollments</a>
                    <div class="d-flex gap-1">
                        <form action="{{ route('automation-sequences.run-now', $seq) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="Run due steps now"><i class="bi bi-lightning-charge"></i></button>
                        </form>
                        <form action="{{ route('automation-sequences.toggle-active', $seq) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ $seq->is_active ? 'Pause' : 'Activate' }}">
                                <i class="bi {{ $seq->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                            </button>
                        </form>
                        <a href="{{ route('automation-sequences.edit', $seq) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('automation-sequences.destroy', $seq) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this sequence?')">
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
