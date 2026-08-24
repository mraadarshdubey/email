@extends('layouts.app')

@section('title', 'Forms - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Forms</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Forms</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Share a link — anyone who signs up becomes a contact and can trigger your Rules/Sequences</p>
    </div>
    <a href="{{ route('lead-forms.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-lg me-1"></i>New Form
    </a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if($forms->isEmpty())
    <div class="card">
        <div class="card-body text-center" style="padding:48px 20px;color:#64748b;">
            <i class="bi bi-ui-checks-grid" style="font-size:32px;"></i>
            <p style="margin:12px 0 16px;font-size:13px;">No forms yet. Create one, share the link anywhere, and every signup flows straight into your automations.</p>
            <a href="{{ route('lead-forms.create') }}" class="btn btn-primary btn-sm">Create Your First Form</a>
        </div>
    </div>
@else
<div class="row g-3">
    @foreach($forms as $form)
    @php $publicUrl = route('lead-forms.public.show', $form->slug); @endphp
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body" style="padding:20px;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h6 style="font-size:14px;font-weight:600;margin:0;">{{ $form->name }}</h6>
                    <span class="badge {{ $form->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $form->is_active ? 'Active' : 'Paused' }}</span>
                </div>

                <div class="d-flex align-items-center" style="gap:8px;margin-bottom:14px;">
                    <input type="text" readonly value="{{ $publicUrl }}" class="form-control form-control-sm copy-link-input" style="font-size:12px;" onclick="this.select()">
                    <button type="button" class="btn btn-sm btn-outline-primary copy-link-btn" data-url="{{ $publicUrl }}" style="flex-shrink:0;">Copy</button>
                    <a href="{{ $publicUrl }}" target="_blank" class="btn btn-sm btn-outline-primary" style="flex-shrink:0;"><i class="bi bi-box-arrow-up-right"></i></a>
                </div>

                <div style="font-size:12px;color:#64748b;margin-bottom:16px;">
                    Tags submitter as
                    @if($form->tag)
                        <span class="badge" style="background:{{ $form->tag->color }}22;color:{{ $form->tag->color }};">{{ $form->tag->name }}</span>
                    @else
                        <span>&mdash; (no tag)</span>
                    @endif
                </div>

                <div class="d-flex justify-content-between align-items-center" style="font-size:11px;color:#64748b;border-top:1px solid rgba(255,255,255,0.08);padding-top:12px;">
                    <span>{{ $form->submissions_count }} submission{{ $form->submissions_count == 1 ? '' : 's' }}</span>
                    <div class="d-flex gap-1">
                        <form action="{{ route('lead-forms.toggle-active', $form) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-primary" title="{{ $form->is_active ? 'Pause' : 'Activate' }}">
                                <i class="bi {{ $form->is_active ? 'bi-pause' : 'bi-play' }}"></i>
                            </button>
                        </form>
                        <a href="{{ route('lead-forms.edit', $form) }}" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="{{ route('lead-forms.destroy', $form) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this form?')">
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

<script>
document.querySelectorAll('.copy-link-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        navigator.clipboard.writeText(btn.dataset.url).then(function() {
            var original = btn.innerText;
            btn.innerText = 'Copied!';
            setTimeout(function() { btn.innerText = original; }, 1500);
        });
    });
});
</script>
@endsection
