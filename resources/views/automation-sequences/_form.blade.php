@php
    $existingSteps = old('steps', isset($sequence) ? $sequence->steps->map(fn($s) => ['email_template_id' => $s->email_template_id, 'delay_minutes' => $s->delay_minutes])->toArray() : [['email_template_id' => '', 'delay_minutes' => 0]]);
@endphp

<div class="card mb-3">
    <div class="card-body" style="padding:24px;">
        <div class="mb-3">
            <label class="form-label">Sequence Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $sequence->name ?? '') }}" placeholder="e.g. New lead nurture" required>
            @error('name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Trigger: contact tagged</label>
                <select name="trigger_tag_id" class="form-select" required>
                    <option value="">Select a tag</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ old('trigger_tag_id', $sequence->trigger_tag_id ?? '') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
                @error('trigger_tag_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                @if($tags->isEmpty())
                    <div style="font-size:12px;color:#d97706;margin-top:4px;">No tags yet — <a href="{{ route('tags.create') }}">create one</a> first.</div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">Send from account</label>
                <select name="email_account_id" class="form-select" required>
                    <option value="">Select an account</option>
                    @foreach($emailAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('email_account_id', $sequence->email_account_id ?? '') == $account->id ? 'selected' : '' }}>{{ $account->email }}</option>
                    @endforeach
                </select>
                @error('email_account_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $sequence->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active" style="font-size:13px;">Active</label>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span class="card-title">Steps</span>
        <button type="button" class="btn btn-sm btn-outline-primary" id="addStepBtn"><i class="bi bi-plus-lg"></i> Add Step</button>
    </div>
    <div class="card-body" style="padding:20px;">
        @if($templates->isEmpty())
            <div style="font-size:12px;color:#d97706;">No active templates yet — <a href="{{ route('email-templates.create') }}">create one</a> first.</div>
        @endif
        <div id="stepsContainer"></div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">{{ isset($sequence) ? 'Save Changes' : 'Create Sequence' }}</button>
    <a href="{{ route('automation-sequences.index') }}" class="btn btn-outline-primary">Cancel</a>
</div>

<template id="stepRowTemplate">
    <div class="step-row d-flex align-items-end gap-2 mb-3" style="border:1px solid #e2e8f0;border-radius:8px;padding:14px;background:#f8fafc;">
        <div style="width:28px;flex-shrink:0;text-align:center;font-size:12px;font-weight:600;color:#94a3b8;" class="step-number">#</div>
        <div style="flex:1;">
            <label class="form-label" style="font-size:11px;">Wait (minutes) before this email</label>
            <input type="number" min="0" class="form-control form-control-sm delay-input" placeholder="0 = send immediately">
        </div>
        <div style="flex:2;">
            <label class="form-label" style="font-size:11px;">Send template</label>
            <select class="form-select form-select-sm template-select">
                <option value="">Select a template</option>
                @foreach($templates as $template)
                    <option value="{{ $template->id }}">{{ $template->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="btn btn-sm btn-outline-danger remove-step-btn" title="Remove step"><i class="bi bi-trash"></i></button>
    </div>
</template>

<script>
(function() {
    const container = document.getElementById('stepsContainer');
    const template = document.getElementById('stepRowTemplate');
    const existingSteps = @json($existingSteps);

    function renumber() {
        container.querySelectorAll('.step-row').forEach((row, i) => {
            row.querySelector('.step-number').textContent = (i + 1);
            row.querySelector('.delay-input').name = `steps[${i}][delay_minutes]`;
            row.querySelector('.template-select').name = `steps[${i}][email_template_id]`;
        });
    }

    function addStep(data) {
        const node = template.content.cloneNode(true);
        const row = node.querySelector('.step-row');
        row.querySelector('.delay-input').value = data?.delay_minutes ?? 0;
        if (data?.email_template_id) {
            row.querySelector('.template-select').value = data.email_template_id;
        }
        row.querySelector('.remove-step-btn').addEventListener('click', () => {
            row.remove();
            renumber();
        });
        container.appendChild(node);
        renumber();
    }

    document.getElementById('addStepBtn').addEventListener('click', () => addStep());

    if (existingSteps.length > 0) {
        existingSteps.forEach(addStep);
    } else {
        addStep();
    }
})();
</script>
