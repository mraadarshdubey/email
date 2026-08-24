@extends('layouts.app')

@section('title', 'Individual Emails - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Individual Emails</li>
@endsection

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4" style="gap:12px;">
    <div>
        <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Send Individual Emails</h1>
        <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Send personalized emails to specific recipients</p>
    </div>
</div>

<div class="row g-3">
    <div class="col-12 col-lg-8">
        <form id="individualEmailForm" method="POST" action="{{ route('individual-emails.send') }}">
            @csrf

            <div class="card mb-3">
                <div class="card-header"><h5 class="card-title">Email Configuration</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="email_account_id" class="form-label">Send From <span style="color:#ef4444;">*</span></label>
                            <select class="form-select @error('email_account_id') is-invalid @enderror" id="email_account_id" name="email_account_id" required>
                                <option value="">Select account</option>
                                @foreach($emailAccounts as $account)
                                    <option value="{{ $account->id }}" {{ old('email_account_id') == $account->id ? 'selected' : '' }}>{{ $account->email }} ({{ $account->from_name }})</option>
                                @endforeach
                            </select>
                            @error('email_account_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="send_type" class="form-label">Sending Type</label>
                            <select class="form-select" id="send_type" name="send_type" required>
                                <option value="individual" {{ old('send_type', 'individual') == 'individual' ? 'selected' : '' }}>Individual (separate per recipient)</option>
                                <option value="bulk" {{ old('send_type') == 'bulk' ? 'selected' : '' }}>Bulk (all in one)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <h5 class="card-title">Recipients</h5>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="validateEmails">Validate Emails</button>
                </div>
                <div class="card-body">
                    @if($contacts->count() > 0)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span style="font-size:13px;font-weight:500;">Select from Contacts</span>
                            <div class="d-flex gap-1">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="selectAllContacts()">All</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="clearAllContacts()">Clear</button>
                            </div>
                        </div>
                        <select class="form-select form-select-sm mb-2" id="tagFilter" onchange="filterContactsByTag()">
                            <option value="">All Tags</option>
                            @foreach($tags as $tag)<option value="{{ $tag->id }}">{{ $tag->name }}</option>@endforeach
                        </select>
                        <div class="contact-list" style="max-height:250px;overflow-y:auto;border:1px solid #e2e8f0;border-radius:8px;padding:8px;">
                            @foreach($contacts as $contact)
                                <div class="contact-item" data-tags="{{ $contact->tags->pluck('id')->implode(',') }}" style="padding:5px 8px;">
                                    <label style="display:flex;align-items:center;gap:6px;cursor:pointer;font-size:12px;flex-wrap:nowrap;min-width:0;">
                                        <input type="checkbox" class="form-check-input contact-checkbox" value="{{ $contact->email }}" data-name="{{ $contact->full_name }}" style="margin:0;flex-shrink:0;">
                                        <span style="font-weight:500;color:#0f172a;white-space:nowrap;flex-shrink:0;">{{ $contact->full_name }}</span>
                                        <span style="color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;flex-shrink:1;min-width:0;">{{ $contact->email }}</span>
                                        <span style="display:flex;align-items:center;gap:4px;flex-shrink:0;">
                                            @foreach($contact->tags->take(2) as $tag)
                                                <span class="tag-chip" data-tag-id="{{ $tag->id }}" style="display:inline-flex;align-items:center;gap:2px;padding:1px 6px;border-radius:3px;background:{{ $tag->color }}18;color:{{ $tag->color }};font-size:10px;font-weight:500;cursor:pointer;white-space:nowrap;" onclick="event.stopPropagation();filterContactsByTagId({{ $tag->id }})" title="Filter by {{ $tag->name }}">
                                                    <span style="width:5px;height:5px;border-radius:50%;background:{{ $tag->color }};flex-shrink:0;"></span>{{ $tag->name }}
                                                </span>
                                            @endforeach
                                            @if($contact->tags->count() > 2)
                                                <span style="font-size:10px;color:#94a3b8;cursor:pointer;white-space:nowrap;"
                                                      onclick="event.stopPropagation();showContactTags(this)" title="View all tags"
                                                      data-contact-name="{{ e($contact->full_name) }}"
                                                      data-tags='@json($contact->tags->map(fn($t)=>["name"=>$t->name,"color"=>$t->color]))'>+{{ $contact->tags->count() - 2 }}</span>
                                            @endif
                                        </span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-2" style="display:flex;align-items:center;gap:12px;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="addSelectedContacts()">Add Selected</button>
                            <span style="font-size:12px;color:#94a3b8;"><span id="selectedContactsCount">0</span> selected</span>
                        </div>
                        <hr style="border-color:#e2e8f0;margin:12px 0;">
                    </div>
                    @endif

                    <label for="recipients" class="form-label">Email Addresses <span style="color:#ef4444;">*</span></label>
                    <textarea class="form-control @error('recipients') is-invalid @enderror" id="recipients" name="recipients" rows="5"
                              placeholder="email1@example.com, email2@example.com&#10;email3@example.com" required>{{ old('recipients', $preSelectedEmails) }}</textarea>
                    @error('recipients')<div class="invalid-feedback">{{ $message }}</div>@enderror

                    <div id="emailValidation" class="d-none mt-2" style="font-size:12px;">
                        <span style="color:#16a34a;font-weight:600;" id="validCount">0</span> valid &middot;
                        <span style="color:#ef4444;font-weight:600;" id="invalidCount">0</span> invalid &middot;
                        <span style="font-weight:600;" id="totalCount">0</span> total
                        <div id="invalidEmailsList" class="d-none mt-1"></div>
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header" style="display:flex;align-items:center;justify-content:space-between;">
                    <h5 class="card-title">Email Content</h5>
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
                <div class="card-body">
                    <div class="mb-3">
                        <label for="subject" class="form-label">Subject Line <span style="color:#ef4444;">*</span></label>
                        <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" placeholder="Email subject" required style="font-size:16px;padding:10px 14px;">
                        @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                        <textarea id="body" name="body" class="form-control d-none" required>{{ old('body') }}</textarea>
                        @error('body')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3 d-none" id="codeEditorContainer">
                        <label for="bodyCode" class="form-label">HTML Code <span style="color:#ef4444;">*</span></label>
                        <textarea id="bodyCode" class="form-control font-monospace @error('body') is-invalid @enderror" rows="14" style="font-size:13px;">{{ old('body') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-primary" id="resetForm">Reset</button>
                <button type="submit" class="btn btn-primary">Send Emails</button>
            </div>
        </form>
    </div>

    <div class="col-12 col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title">Template</h5></div>
            <div class="card-body" style="padding:16px;">
                <div style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;margin-bottom:14px;">
                    <div style="width:100%;height:120px;overflow:hidden;background:#f1f5f9;position:relative;">
                        <iframe id="liveThumbFrame" style="width:357%;height:429px;transform:scale(0.28);transform-origin:top left;border:0;pointer-events:none;background:#fff;"></iframe>
                    </div>
                </div>
                <div class="d-grid gap-2">
                    @if($templates->count() > 0)
                        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#templatePickerModal">
                            <i class="bi bi-grid me-1"></i>Browse Templates
                        </button>
                    @endif
                    <button type="button" class="btn btn-outline-primary btn-sm" id="saveAsTemplateBtn">
                        <i class="bi bi-save me-1"></i>Save as Template
                    </button>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title">Email Accounts</h5></div>
            <div class="card-body" style="font-size:13px;">
                @forelse($emailAccounts as $account)
                    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px;">
                        <div style="width:32px;height:32px;border-radius:6px;background:#f1f5f9;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="bi bi-envelope" style="color:#64748b;"></i>
                        </div>
                        <div style="min-width:0;">
                            <div style="font-weight:500;color:#0f172a;">{{ $account->from_name }}</div>
                            <div style="color:#94a3b8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $account->email }}</div>
                        </div>
                    </div>
                @empty
                    <p style="color:#94a3b8;margin:0;">No accounts. <a href="{{ route('email-accounts.create') }}">Add one</a>.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

@if($templates->count() > 0)
<div class="modal fade" id="templatePickerModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Choose a template</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="background:#f8fafc;">
                <div class="row g-3">
                    @foreach($templates as $template)
                    <div class="col-sm-6 col-lg-4 col-xl-3">
                        <div class="card template-picker-card" role="button"
                             data-subject="{{ $template->subject }}"
                             data-body="{{ base64_encode($template->body) }}"
                             data-bs-dismiss="modal"
                             style="overflow:hidden;transition:box-shadow .15s,border-color .15s;">
                            @include('partials._template-thumb', ['template' => $template])
                            <div style="padding:10px 12px;">
                                <div style="font-size:13px;font-weight:600;color:#0f172a;">{{ $template->name }}</div>
                                <div style="font-size:11px;color:#94a3b8;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $template->subject }}</div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
<style>
    .template-picker-card:hover { box-shadow:0 4px 12px rgba(15,23,42,0.1); border-color:#0f172a; }
</style>
@endif
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    #quillToolbar.ql-toolbar.ql-snow { border: 1px solid #e2e8f0; border-bottom: none; border-radius: 8px 8px 0 0; background: #f8fafc; }
    #quillEditor.ql-container.ql-snow { border: 1px solid #e2e8f0; border-radius: 0 0 8px 8px; min-height: 300px; font-family: Inter, Arial, sans-serif; font-size: 14px; background: #fff; }
    #quillEditor .ql-editor { min-height: 300px; color: #0f172a; }
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

    function updateThumb(html) {
        $('#liveThumbFrame').attr('srcdoc', html || '<div style="font-family:sans-serif;color:#cbd5e1;padding:40px;text-align:center;">Empty</div>');
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
        if ($('#codeMode').is(':checked')) { $('#bodyCode').val(getContent()); $('#visualEditorContainer').addClass('d-none'); $('#codeEditorContainer').removeClass('d-none'); }
        else { setContent($('#bodyCode').val()); $('#codeEditorContainer').addClass('d-none'); $('#visualEditorContainer').removeClass('d-none'); }
    });
    $('#bodyCode').on('input', function() { $('#body').val($(this).val()); updateThumb($(this).val()); });

    function decodeBase64(s) { try { return new TextDecoder('utf-8').decode(new Uint8Array([...atob(s)].map(function(c) { return c.charCodeAt(0); }))); } catch(e) { try { return atob(s); } catch(e2) { return s; } } }

    $('.template-picker-card').on('click', function(e) {
        e.preventDefault();
        $('#subject').val($(this).data('subject'));
        setContent(decodeBase64($(this).data('body')));
        $('#visualMode').prop('checked', true).trigger('change');
    });

    // ── Save current draft as a reusable template ──
    $('#saveAsTemplateBtn').on('click', function() {
        Swal.fire({
            title: 'Save as Template',
            input: 'text',
            inputLabel: 'Template name',
            inputPlaceholder: 'e.g. My Custom Design',
            showCancelButton: true,
            confirmButtonText: 'Save',
            preConfirm: (name) => { if (!name) Swal.showValidationMessage('Please enter a name'); return name; }
        }).then(function(result) {
            if (!result.isConfirmed) return;
            var subject = $('#subject').val() || result.value;
            var body = getContent();
            if (!body) { Swal.fire({ icon: 'warning', title: 'Nothing to save', text: 'Write some content first.' }); return; }
            $.ajax({
                url: '{{ route("email-templates.store") }}', method: 'POST', dataType: 'json',
                data: { _token: '{{ csrf_token() }}', name: result.value, subject: subject, body: body },
                success: function(res) {
                    if (res.success) Swal.fire({ icon: 'success', title: 'Saved!', text: 'Template "' + result.value + '" is ready to reuse.', timer: 1800, showConfirmButton: false });
                },
                error: function(xhr) {
                    var msg = (xhr.responseJSON && xhr.responseJSON.errors) ? Object.values(xhr.responseJSON.errors).flat().join(' ') : ((xhr.responseJSON && xhr.responseJSON.message) || 'Could not save template.');
                    Swal.fire({ icon: 'error', title: 'Failed', text: msg });
                }
            });
        });
    });

    // ── Email validation ──

    $('#validateEmails').on('click', function() {
        var r = $('#recipients').val().trim(); if (!r) return;
        var b = $(this); b.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>');
        $.ajax({ url: '{{ route("individual-emails.validate") }}', method: 'POST', data: { recipients: r, _token: '{{ csrf_token() }}' },
            success: function(res) {
                $('#validCount').text(res.valid_count); $('#invalidCount').text(res.invalid_count);
                $('#totalCount').text(res.valid_count + res.invalid_count); $('#emailValidation').removeClass('d-none');
                if (res.invalid_count > 0) { var h = ''; res.invalid_emails.forEach(function(e) { h += '<span class="badge bg-danger me-1 mb-1">' + e + '</span>'; }); $('.invalid-emails-container').html(h); $('#invalidEmailsList').removeClass('d-none'); }
                else $('#invalidEmailsList').addClass('d-none');
            },
            error: function(xhr) { Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON && xhr.responseJSON.message) || 'Validation failed' }); },
            complete: function() { b.prop('disabled', false).html('Validate Emails'); }
        });
    });

    $('#individualEmailForm').on('submit', function(e) {
        e.preventDefault();
        var fd = new FormData(this); fd.set('body', getContent());
        Swal.fire({ title: 'Sending...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({ url: $(this).attr('action'), method: 'POST', data: fd, processData: false, contentType: false,
            success: function(res) {
                if (res.success) { Swal.fire({ icon: 'success', title: 'Sent!', text: 'Queued ' + res.summary.total_emails + ' emails.' }).then(() => { $('#individualEmailForm')[0].reset(); setContent(''); }); }
                else Swal.fire({ icon: 'error', title: 'Failed', text: res.message });
            },
            error: function(xhr) { Swal.fire({ icon: 'error', title: 'Error', text: (xhr.responseJSON && xhr.responseJSON.message) || 'An error occurred' }); }
        });
    });

    $('#resetForm').on('click', function() {
        Swal.fire({ title: 'Reset?', text: 'Clear all data?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes' }).then(function(r) {
            if (r.isConfirmed) { $('#individualEmailForm')[0].reset(); setContent(''); $('#emailValidation').addClass('d-none'); }
        });
    });

    function uc() { $('#selectedContactsCount').text($('.contact-checkbox:checked').length); }
    window.selectAllContacts = function() { $('.contact-item:visible .contact-checkbox').prop('checked', true); uc(); };
    window.clearAllContacts = function() { $('.contact-checkbox').prop('checked', false); uc(); };
    window.filterContactsByTag = function() {
        var t = $('#tagFilter').val(); $('.contact-item').each(function() { var tags = $(this).data('tags').toString().split(','); if (!t || tags.includes(t)) $(this).show(); else { $(this).hide(); $(this).find('.contact-checkbox').prop('checked', false); } }); uc();
    };
    window.filterContactsByTagId = function(tagId) {
        $('#tagFilter').val(tagId); window.filterContactsByTag();
    };
    window.showContactTags = function(el) {
        var name = el.dataset.contactName;
        var tags = JSON.parse(el.dataset.tags);
        var html = tags.map(function(t) { return '<span style="display:inline-flex;align-items:center;gap:4px;margin:2px;"><span style="width:8px;height:8px;border-radius:50%;background:' + t.color + ';"></span>' + t.name + '</span>'; }).join('');
        Swal.fire({ title: name, html: '<div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:center;">' + html + '</div>', confirmButtonText: 'Close' });
    };
    window.addSelectedContacts = function() {
        var emails = []; $('.contact-checkbox:checked').each(function() { emails.push($(this).val()); });
        if (!emails.length) return;
        var cur = $('#recipients').val().trim(); $('#recipients').val(cur ? cur + ', ' + emails.join(', ') : emails.join(', '));
        $('.contact-checkbox').prop('checked', false); uc();
        Swal.fire({ icon: 'success', title: 'Added', text: emails.length + ' contact(s) added.', timer: 1200, showConfirmButton: false });
    };

    $('.contact-checkbox').on('change', uc); uc();
});
</script>
@endpush
