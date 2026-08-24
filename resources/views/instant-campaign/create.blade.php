@extends('layouts.app')

@section('title', 'Instant Campaign - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Instant Campaign</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Create Instant Campaign</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Upload an Excel file and send bulk emails instantly.</p>
    </div>
    <a href="{{ route('email-templates.index') }}" class="btn btn-outline-primary btn-sm">Manage Templates</a>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger">
        <strong>Please fix the following errors:</strong>
        <ul class="mb-0 mt-1 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Campaign Setup</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('instant.campaign.import') }}" method="POST" enctype="multipart/form-data" id="campaignForm">
                    @csrf

                    @if($templates->count() > 0)
                        <div class="mb-3">
                            <label for="template_select" class="form-label">Email Template <span class="text-muted">(Optional)</span></label>
                            <select class="form-select" id="template_select" name="template_id">
                                <option value="">Select a template</option>
                                @foreach($templates as $template)
                                    <option value="{{ $template->id }}"
                                            data-subject="{{ $template->subject }}"
                                            data-body="{{ base64_encode($template->body) }}"
                                            {{ $selectedTemplate && $selectedTemplate->id == $template->id ? 'selected' : '' }}>
                                        {{ $template->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="file" class="form-label">Excel/CSV File <span style="color:#ef4444;">*</span></label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror"
                               id="file" name="file" accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div style="font-size:11px;color:#94a3b8;margin-top:4px;">
                            Format: Given Name | Family Name | eMail Address | Company | Phone | Notes. Max 10MB.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="subject" class="form-label">Email Subject <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror"
                               id="subject" name="subject"
                               value="{{ old('subject', $selectedTemplate ? $selectedTemplate->subject : '') }}"
                               placeholder="Enter your email subject line" required>
                        @error('subject')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <label class="form-label mb-0">Email Body <span style="color:#ef4444;">*</span></label>
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

                    <div class="mb-3" id="visualEditorContainer">
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
                        <textarea id="body" name="body" class="form-control d-none">{{ old('body', $selectedTemplate ? $selectedTemplate->body : '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 d-none" id="codeEditorContainer">
                        <label for="bodyCode" class="form-label">HTML Code <span style="color:#ef4444;">*</span></label>
                        <textarea id="bodyCode" class="form-control font-monospace @error('body') is-invalid @enderror"
                                  rows="18" placeholder="Enter your HTML code..."
                                  style="font-size:13px;">{{ old('body', $selectedTemplate ? $selectedTemplate->body : '') }}</textarea>
                        @error('body')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <button type="reset" class="btn btn-outline-primary" id="resetForm">Reset</button>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-send me-1"></i>Send Campaign
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Guidelines</h5>
            </div>
            <div class="card-body" style="font-size:13px;">
                <ul class="list-unstyled mb-0" style="display:flex;flex-direction:column;gap:8px;">
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Standard format: Given Name, Family Name, eMail Address, Company, Phone, Notes</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Email column C is required; others optional</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> Max 10,000 recipients per campaign</li>
                    <li style="display:flex;gap:8px;align-items:flex-start;"><span style="color:#16a34a;">&#10003;</span> .xlsx, .xls, or .csv formats</li>
                </ul>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">File Format</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr>
                                <th style="background:#0f172a;color:#fff;">A</th>
                                <th style="background:#0f172a;color:#fff;">B</th>
                                <th style="background:#0f172a;color:#fff;">C*</th>
                                <th style="background:#0f172a;color:#fff;">D</th>
                                <th style="background:#0f172a;color:#fff;">E</th>
                                <th style="background:#0f172a;color:#fff;">F</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style="font-weight:500;">Given Name</td>
                                <td style="font-weight:500;">Family Name</td>
                                <td style="font-weight:600;color:#0f172a;">eMail Address</td>
                                <td style="font-weight:500;">Company</td>
                                <td style="font-weight:500;">Phone</td>
                                <td style="font-weight:500;">Notes</td>
                            </tr>
                            <tr>
                                <td>John</td><td>Doe</td>
                                <td style="color:#0f172a;">john@example.com</td>
                                <td>ABC Corp</td><td>+1234567890</td><td>Sales</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($templates->count() > 0)
        <div class="card">
            <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                <h5 class="card-title">Templates</h5>
                <a href="{{ route('email-templates.index') }}" style="font-size:12px;color:#64748b;text-decoration:none;">Browse all &rarr;</a>
            </div>
            <div class="card-body" style="padding:12px;">
                <div class="row g-2">
                    @foreach($templates->take(6) as $i => $template)
                    <div class="col-6">
                        <div class="tpl-card template-quick-btn"
                             data-template-id="{{ $template->id }}"
                             data-subject="{{ $template->subject }}"
                             data-body="{{ base64_encode($template->body) }}">
                            @include('partials._template-thumb', ['template' => $template, 'height' => 90])
                            <div class="tpl-card-body">
                                <div class="tpl-name">{{ $template->name }}</div>
                                <div class="tpl-subject">{{ Str::limit($template->subject, 35) ?: 'Ready to use' }}</div>
                                <div class="tpl-action">Apply Template</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
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
    /* Template cards */
    .tpl-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        cursor: pointer;
        text-align: center;
        transition: all 0.2s;
        height: 100%;
    }
    .tpl-card:hover {
        border-color: #0f172a;
        box-shadow: 0 4px 16px rgba(15,23,42,0.08);
        transform: translateY(-2px);
    }
    .tpl-card-body {
        padding: 10px 12px 12px;
    }
    .tpl-name {
        font-size: 13px;
        font-weight: 600;
        color: #0f172a;
        line-height: 1.2;
        margin-bottom: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tpl-subject {
        font-size: 11px;
        color: #94a3b8;
        line-height: 1.3;
        margin-bottom: 10px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .tpl-action {
        font-size: 10px;
        font-weight: 600;
        color: #0f172a;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        opacity: 0;
        transform: translateY(4px);
        transition: all 0.2s;
    }
    .tpl-card:hover .tpl-action {
        opacity: 1;
        transform: translateY(0);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
$(document).ready(function() {

    // ── Simple rich-text editor (Quill) ──
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

    function syncGrapesToTextarea() {
        var html = quill.root.innerHTML;
        if (html === '<p><br></p>') html = '';
        $('#body').val(html);
    }

    function getGrapesContent() {
        if ($('#codeMode').is(':checked')) return $('#bodyCode').val() || '';
        return $('#body').val() || '';
    }

    function setGrapesContent(htmlContent) {
        var inner = stripWrapper(htmlContent || '');
        quill.clipboard.dangerouslyPasteHTML(inner);
        $('#bodyCode').val(htmlContent || '');
        $('#body').val(htmlContent || '');
    }

    quill.on('text-change', syncGrapesToTextarea);
    setGrapesContent($('#body').val() || '');

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
                    setGrapesContent(res.body);
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

    // ── Visual / Code mode toggle ──
    $('input[name="editorMode"]').on('change', function() {
        if ($('#codeMode').is(':checked')) {
            $('#bodyCode').val(getGrapesContent());
            $('#visualEditorContainer').addClass('d-none');
            $('#codeEditorContainer').removeClass('d-none');
        } else {
            setGrapesContent($('#bodyCode').val());
            $('#codeEditorContainer').addClass('d-none');
            $('#visualEditorContainer').removeClass('d-none');
        }
    });

    // Sync code editor changes back to hidden textarea
    $('#bodyCode').on('input', function() {
        $('#body').val($(this).val());
    });

    // ── Base64 decode helper ──
    function decodeBase64(str) {
        try {
            const binary = atob(str);
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) bytes[i] = binary.charCodeAt(i);
            return new TextDecoder('utf-8').decode(bytes);
        } catch (e) {
            try { return atob(str); } catch (e2) { return str; }
        }
    }

    // ── Template selection ──
    $('#template_select').on('change', function() {
        const opt = $(this).find('option:selected');
        if (!opt.val()) return;
        $('#subject').val(opt.data('subject'));
        setGrapesContent(decodeBase64(opt.data('body')));
        // Ensure visual mode
        $('#visualMode').prop('checked', true).trigger('change');
    });

    // ── Quick template buttons ──
    $('.template-quick-btn').on('click', function() {
        const btn = $(this);
        $('#template_select').val(btn.data('template-id'));
        $('#subject').val(btn.data('subject'));
        setGrapesContent(decodeBase64(btn.data('body')));
        $('#visualMode').prop('checked', true).trigger('change');
    });

    // ── Form submission ──
    $('#campaignForm').on('submit', function(e) {
        // Sync content from active mode
        if ($('#visualMode').is(':checked')) {
            $('#body').val(getGrapesContent());
        } else {
            $('#body').val($('#bodyCode').val());
        }

        const file = $('#file')[0].files[0];
        const subject = $('#subject').val().trim();
        const body = $('#body').val().trim();

        if (!file || !subject || !body) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Required Fields Missing',
                text: 'Please fill in all required fields and upload an Excel file.',
            });
            return false;
        }

        if (file) {
            const fileSize = file.size / 1024 / 1024;
            const allowedTypes = [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv'
            ];

            if (fileSize > 10) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'File Too Large', text: 'Please upload a file smaller than 10MB.' });
                return false;
            }

            if (!allowedTypes.includes(file.type)) {
                e.preventDefault();
                Swal.fire({ icon: 'error', title: 'Invalid File Type', text: 'Please upload a valid Excel or CSV file.' });
                return false;
            }
        }

        $('#submitBtn').html('<span class="spinner-border spinner-border-sm me-2"></span>Sending...').prop('disabled', true);

        Swal.fire({
            title: 'Sending Campaign...',
            text: 'Please wait while we process your campaign.',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    });

    // ── Reset ──
    $('#resetForm').on('click', function() {
        $('#template_select').val('');
        setGrapesContent('');
    });

    // ── Pre-loaded template notification ──
    @if($selectedTemplate)
        setTimeout(() => {
            Swal.fire({
                icon: 'info',
                title: 'Template Loaded',
                text: '"{{ $selectedTemplate->name }}" has been pre-loaded.',
                timer: 2500,
                showConfirmButton: false
            });
        }, 500);
    @endif

});
</script>
@endpush
