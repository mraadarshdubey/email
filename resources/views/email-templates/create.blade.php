@extends('layouts.app')

@section('title', 'Create Template - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-templates.index') }}">Templates</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">New Template</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Design a reusable template for your campaigns</p>
    </div>
    <a href="{{ route('email-templates.index') }}" class="btn btn-outline-primary btn-sm">Back to Templates</a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
        </ul>
    </div>
@endif

<form action="{{ route('email-templates.store') }}" method="POST" id="templateForm">
    @csrf
    <div class="row g-3">
        <div class="col-12 col-lg-8">
            <div class="card">
                <div class="card-body" style="padding:24px;">
                    <div class="mb-3">
                        <label for="name" class="form-label">Template Name <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Welcome Email" required style="font-size:16px;padding:10px 14px;">
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject Line <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Subject line" required>
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">Content <span style="color:#ef4444;">*</span></label>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <button type="button" class="btn btn-sm btn-outline-primary" id="aiDraftBtn">✨ AI Draft</button>
                            <div class="btn-group btn-group-sm">
                                <input type="radio" class="btn-check" name="editorMode" id="visualMode" checked autocomplete="off">
                                <label class="btn btn-outline-primary" for="visualMode">Design</label>
                                <input type="radio" class="btn-check" name="editorMode" id="codeMode" autocomplete="off">
                                <label class="btn btn-outline-primary" for="codeMode">HTML</label>
                            </div>
                        </div>
                    </div>

                    <div id="visualEditorContainer">
                        <div id="quillToolbar">
                            <span class="ql-formats">
                                <button class="ql-bold"></button>
                                <button class="ql-italic"></button>
                                <button class="ql-underline"></button>
                                <button class="ql-strike"></button>
                            </span>
                            <span class="ql-formats">
                                <select class="ql-align"></select>
                            </span>
                            <span class="ql-formats">
                                <select class="ql-size"></select>
                                <select class="ql-color"></select>
                            </span>
                            <span class="ql-formats">
                                <button class="ql-link"></button>
                            </span>
                            <span class="ql-formats">
                                <select id="personalizeSelect">
                                    <option value="" selected disabled>Personalize</option>
                                    <option value="@{{ contact.first_name }}">First name</option>
                                    <option value="@{{ contact.last_name }}">Last name</option>
                                    <option value="@{{ contact.email }}">Email</option>
                                </select>
                            </span>
                        </div>
                        <div id="quillEditor"></div>
                        <textarea id="body" name="body" class="form-control d-none" required>{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-none" id="codeEditorContainer">
                        <textarea id="bodyCode" class="form-control font-monospace @error('body') is-invalid @enderror" rows="18" placeholder="Enter your HTML code..." style="font-size:13px;">{{ old('body') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-4">
            <div class="card mb-3">
                <div class="card-header"><h5 class="card-title">Preview</h5></div>
                <div class="card-body" style="padding:0;">
                    <div style="width:100%;height:180px;overflow:hidden;background:#f1f5f9;position:relative;">
                        <iframe id="liveThumbFrame" style="width:357%;height:643px;transform:scale(0.28);transform-origin:top left;border:0;pointer-events:none;background:#fff;"></iframe>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header"><h5 class="card-title">Details</h5></div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Brief description (optional)">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active" style="font-size:13px;">Active (available for campaigns)</label>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">Save Template</button>
                <a href="{{ route('email-templates.index') }}" class="btn btn-outline-primary">Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    #quillToolbar.ql-toolbar.ql-snow { border: 1px solid #e2e8f0; border-bottom: none; border-radius: 8px 8px 0 0; background: #f8fafc; }
    #quillEditor.ql-container.ql-snow { border: 1px solid #e2e8f0; border-radius: 0 0 8px 8px; min-height: 380px; font-family: Inter, Arial, sans-serif; font-size: 14px; background: #fff; }
    #quillEditor .ql-editor { min-height: 380px; color: #0f172a; }
    #quillEditor .ql-editor.ql-blank::before { color: #94a3b8; font-style: normal; }
    #quillToolbar.ql-toolbar.ql-snow { color: #0f172a; }
    #quillToolbar .ql-stroke { stroke: #0f172a; }
    #quillToolbar .ql-fill { fill: #0f172a; }
    #quillToolbar .ql-picker-label { color: #0f172a; }
    #quillToolbar .ql-picker-options { background: #fff; color: #0f172a; }
    #personalizeSelect { border: 1px solid #ccc; border-radius: 3px; font-size: 12px; padding: 2px 4px; color: #444; background: #fff; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
$(document).ready(function() {
    var quill = new Quill('#quillEditor', {
        theme: 'snow',
        modules: { toolbar: '#quillToolbar' },
    });

    function stripWrapper(raw) {
        let h = raw || '';
        var bm = h.match(/<body[^>]*>([\s\S]*?)<\/body>/i);
        if (bm) { h = bm[1]; } else { h = h.replace(/<!DOCTYPE[^>]*>/gi, '').replace(/<\/?html[^>]*>/gi, '').replace(/<head[^>]*>[\s\S]*?<\/head>/gi, ''); }
        h = h.replace(/<style[^>]*>[\s\S]*?<\/style>/gi, '');
        return h.trim();
    }

    function updateThumb(html) {
        $('#liveThumbFrame').attr('srcdoc', html || '<div style="font-family:sans-serif;color:#cbd5e1;padding:40px;text-align:center;">Start typing…</div>');
    }

    function sync() {
        var html = quill.root.innerHTML;
        if (html === '<p><br></p>') html = '';
        $('#body').val(html);
        updateThumb(html);
    }

    function getContent() {
        if ($('#codeMode').is(':checked')) return $('#bodyCode').val() || '';
        return $('#body').val() || '';
    }

    function setContent(html) {
        var inner = stripWrapper(html || '');
        quill.clipboard.dangerouslyPasteHTML(inner);
        $('#bodyCode').val(html || '');
        $('#body').val(html || '');
        updateThumb(html || '');
    }

    quill.on('text-change', sync);
    setContent($('#body').val() || '');

    // ── AI Draft ──
    $('#aiDraftBtn').on('click', function() {
        Swal.fire({
            title: 'AI Draft',
            input: 'textarea',
            inputLabel: 'What should this email be about?',
            inputPlaceholder: 'e.g. Announce our new pricing plan to existing customers, friendly tone',
            showCancelButton: true,
            confirmButtonText: 'Generate',
            preConfirm: (brief) => { if (!brief) Swal.showValidationMessage('Please describe the email'); return brief; }
        }).then(function(result) {
            if (!result.isConfirmed) return;
            Swal.fire({ title: 'Generating draft…', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            $.ajax({
                url: '{{ route("ai.compose-email") }}', method: 'POST', dataType: 'json',
                data: { _token: '{{ csrf_token() }}', brief: result.value },
                success: function(res) {
                    if (res.subject) $('#subject').val(res.subject);
                    setContent(res.body);
                    $('#visualMode').prop('checked', true).trigger('change');
                    Swal.close();
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.message) || 'Could not generate a draft.';
                    Swal.fire({ icon: 'error', title: 'Failed', text: msg });
                }
            });
        });
    });

    $('#personalizeSelect').on('change', function() {
        var token = $(this).val();
        if (!token) return;
        var range = quill.getSelection(true) || { index: quill.getLength(), length: 0 };
        quill.insertText(range.index, token, 'user');
        quill.setSelection(range.index + token.length, 0);
        $(this).val('');
    });

    $('input[name="editorMode"]').on('change', function() {
        if ($('#codeMode').is(':checked')) {
            $('#bodyCode').val(getContent());
            $('#visualEditorContainer').addClass('d-none');
            $('#codeEditorContainer').removeClass('d-none');
        } else {
            setContent($('#bodyCode').val());
            $('#codeEditorContainer').addClass('d-none');
            $('#visualEditorContainer').removeClass('d-none');
        }
    });

    $('#bodyCode').on('input', function() { $('#body').val($(this).val()); updateThumb($(this).val()); });

    $('#templateForm').on('submit', function() {
        if ($('#visualMode').is(':checked')) $('#body').val(getContent());
        else $('#body').val($('#bodyCode').val());
    });
});
</script>
@endpush
