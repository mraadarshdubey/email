@php
    $followup = $followup ?? null;
    $existingWait = $followup->wait_minutes ?? 1440;
    if ($existingWait % 1440 === 0) { $waitValue = $existingWait / 1440; $waitUnit = 'days'; }
    elseif ($existingWait % 60 === 0) { $waitValue = $existingWait / 60; $waitUnit = 'hours'; }
    else { $waitValue = $existingWait; $waitUnit = 'minutes'; }
@endphp

<div class="card">
    <div class="card-body" style="padding:24px;">
        <div class="mb-3">
            <label class="form-label">Follow-up Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $followup->name ?? '') }}" placeholder="e.g. Nudge non-clickers" required>
            @error('name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Which broadcast?</label>
            <select name="source_batch_id" class="form-select" required>
                <option value="">Select a broadcast you've already sent</option>
                @foreach($broadcasts as $b)
                    <option value="{{ $b->batch_id }}" {{ old('source_batch_id', $followup->source_batch_id ?? '') == $b->batch_id ? 'selected' : '' }}>
                        {{ $b->subject }} — {{ \Illuminate\Support\Carbon::parse($b->sent_at)->format('d M, h:i A') }} ({{ $b->recipients }} recipients)
                    </option>
                @endforeach
            </select>
            @error('source_batch_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            @if($broadcasts->isEmpty())
                <div style="font-size:12px;color:#d97706;margin-top:4px;">No broadcasts yet — send an individual email campaign first.</div>
            @endif
        </div>

        <div class="row g-3 mb-2">
            <div class="col-md-4">
                <label class="form-label">IF recipient</label>
                <select name="condition" class="form-select" required>
                    <option value="not_clicked" {{ old('condition', $followup->condition ?? 'not_clicked') == 'not_clicked' ? 'selected' : '' }}>Hasn't clicked</option>
                    <option value="not_opened" {{ old('condition', $followup->condition ?? '') == 'not_opened' ? 'selected' : '' }}>Hasn't opened</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Wait at least</label>
                <input type="number" name="wait_value" min="1" class="form-control" value="{{ old('wait_value', $waitValue) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">Unit</label>
                <select name="wait_unit" class="form-select" required>
                    <option value="minutes" {{ old('wait_unit', $waitUnit) == 'minutes' ? 'selected' : '' }}>Minutes</option>
                    <option value="hours" {{ old('wait_unit', $waitUnit) == 'hours' ? 'selected' : '' }}>Hours</option>
                    <option value="days" {{ old('wait_unit', $waitUnit) == 'days' ? 'selected' : '' }}>Days</option>
                </select>
            </div>
        </div>

        <hr style="margin:20px 0;border-color:rgba(255,255,255,0.08);">

        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <label class="form-label">THEN send template</label>
                <select name="email_template_id" class="form-select" required>
                    <option value="">Select a template</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('email_template_id', $followup->email_template_id ?? '') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
                    @endforeach
                </select>
                @error('email_template_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                @if($templates->isEmpty())
                    <div style="font-size:12px;color:#d97706;margin-top:4px;">No active templates yet — <a href="{{ route('email-templates.create') }}">create one</a> first.</div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">From account</label>
                <select name="email_account_id" class="form-select" required>
                    <option value="">Select an account</option>
                    @foreach($emailAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('email_account_id', $followup->email_account_id ?? '') == $account->id ? 'selected' : '' }}>{{ $account->email }}</option>
                    @endforeach
                </select>
                @error('email_account_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $followup->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active" style="font-size:13px;">Active</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">{{ isset($followup) ? 'Save Changes' : 'Create Follow-up' }}</button>
    <a href="{{ route('email-followups.index') }}" class="btn btn-outline-primary">Cancel</a>
</div>
