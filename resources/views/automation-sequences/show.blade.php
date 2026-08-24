@extends('layouts.app')

@section('title', $sequence->name . ' - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('automation-sequences.index') }}">Sequences</a></li>
    <li class="breadcrumb-item active">{{ $sequence->name }}</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">{{ $sequence->name }}</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Triggered when tagged "{{ $sequence->triggerTag->name ?? '—' }}"</p>
    </div>
    <a href="{{ route('automation-sequences.edit', $sequence) }}" class="btn btn-outline-primary btn-sm">Edit Sequence</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card mb-3">
    <div class="card-header"><span class="card-title">Steps</span></div>
    <div class="card-body" style="padding:0;">
        <table class="table">
            <thead><tr><th>#</th><th>Wait</th><th>Template</th></tr></thead>
            <tbody>
                @foreach($sequence->steps as $i => $step)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $step->delay_minutes == 0 ? 'Immediately' : $step->delay_minutes . ' min after previous step' }}</td>
                    <td>{{ $step->emailTemplate->name ?? '—' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Enrollments ({{ $enrollments->count() }})</span></div>
    <div class="card-body" style="padding:0;">
        @if($enrollments->isEmpty())
            <div class="text-center" style="padding:32px;color:#94a3b8;font-size:13px;">No contacts enrolled yet — tag a contact "{{ $sequence->triggerTag->name ?? '' }}" to enroll them.</div>
        @else
        <table class="table data-table">
            <thead><tr><th>Contact</th><th>Step</th><th>Status</th><th>Next Send</th><th>Enrolled</th></tr></thead>
            <tbody>
                @foreach($enrollments as $e)
                <tr>
                    <td>{{ $e->contact->email ?? '—' }}</td>
                    <td>{{ $e->current_step + 1 }} / {{ $sequence->steps->count() }}</td>
                    <td>
                        <span class="badge {{ $e->status === 'active' ? 'bg-primary' : ($e->status === 'completed' ? 'bg-success' : 'bg-secondary') }}">{{ ucfirst($e->status) }}</span>
                    </td>
                    <td>{{ $e->status === 'active' && $e->next_run_at ? $e->next_run_at->format('d M, h:i A') : '—' }}</td>
                    <td>{{ $e->enrolled_at?->diffForHumans() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
