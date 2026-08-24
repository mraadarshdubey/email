<div class="card">
    <div class="card-body" style="padding:24px;">
        <div class="mb-3">
            <label class="form-label">Rule Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $rule->name ?? '') }}" placeholder="e.g. Welcome new VIP contacts" required>
            @error('name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <label class="form-label">IF (trigger)</label>
                <select name="trigger_type" id="trigger_type" class="form-select" required>
                    <option value="contact_tagged" {{ old('trigger_type', $rule->trigger_type ?? '') == 'contact_tagged' ? 'selected' : '' }}>Contact is tagged</option>
                    <option value="contact_created" {{ old('trigger_type', $rule->trigger_type ?? '') == 'contact_created' ? 'selected' : '' }}>Contact is created</option>
                </select>
            </div>
            <div class="col-md-6" id="tagSelectWrap">
                <label class="form-label">With tag</label>
                <select name="trigger_tag_id" class="form-select">
                    <option value="">Select a tag</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ old('trigger_tag_id', $rule->trigger_tag_id ?? '') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
                @error('trigger_tag_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <hr style="margin:20px 0;">

        <div class="row g-3 mb-2">
            <div class="col-md-6">
                <label class="form-label">THEN send template</label>
                <select name="email_template_id" class="form-select" required>
                    <option value="">Select a template</option>
                    @foreach($templates as $template)
                        <option value="{{ $template->id }}" {{ old('email_template_id', $rule->email_template_id ?? '') == $template->id ? 'selected' : '' }}>{{ $template->name }}</option>
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
                        <option value="{{ $account->id }}" {{ old('email_account_id', $rule->email_account_id ?? '') == $account->id ? 'selected' : '' }}>{{ $account->email }}</option>
                    @endforeach
                </select>
                @error('email_account_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $rule->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active" style="font-size:13px;">Active</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">{{ isset($rule) ? 'Save Changes' : 'Create Rule' }}</button>
    <a href="{{ route('automation-rules.index') }}" class="btn btn-outline-primary">Cancel</a>
</div>

<script>
    (function() {
        const triggerType = document.getElementById('trigger_type');
        const tagWrap = document.getElementById('tagSelectWrap');
        function toggle() {
            tagWrap.style.display = triggerType.value === 'contact_tagged' ? '' : 'none';
        }
        triggerType.addEventListener('change', toggle);
        toggle();
    })();
</script>
