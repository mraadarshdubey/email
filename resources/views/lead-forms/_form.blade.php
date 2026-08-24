@php
    $form = $form ?? null;
    $enabledFields = old('fields', $form->fields_config ?? []);
    $fieldLabels = ['first_name' => 'First name', 'last_name' => 'Last name', 'phone' => 'Phone', 'company' => 'Company'];
@endphp

<div class="card">
    <div class="card-body" style="padding:24px;">
        <div class="mb-3">
            <label class="form-label">Form Name <span style="color:#ef4444;">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $form->name ?? '') }}" placeholder="e.g. Newsletter Signup" required>
            <div style="font-size:11px;color:#64748b;margin-top:4px;">Internal name only — not shown publicly.</div>
            @error('name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Headline</label>
            <input type="text" name="headline" class="form-control" value="{{ old('headline', $form->headline ?? '') }}" placeholder="e.g. Join the newsletter">
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="2" placeholder="Optional subtext shown under the headline">{{ old('description', $form->description ?? '') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Collect these fields (email is always required)</label>
            <div class="d-flex flex-wrap" style="gap:16px;">
                @foreach($fieldLabels as $key => $label)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="fields[]" value="{{ $key }}" id="field_{{ $key }}" {{ in_array($key, $enabledFields) ? 'checked' : '' }}>
                    <label class="form-check-label" for="field_{{ $key }}" style="font-size:13px;">{{ $label }}</label>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Tag submitters with</label>
            <select name="tag_id" class="form-select">
                <option value="">No tag</option>
                @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ old('tag_id', $form->tag_id ?? '') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                @endforeach
            </select>
            <div style="font-size:11px;color:#64748b;margin-top:4px;">
                This is what lets a Rule or Sequence fire automatically when someone submits.
                @if($tags->isEmpty())
                    No tags yet — <a href="{{ route('tags.create') }}">create one</a> first.
                @endif
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Success message</label>
            <input type="text" name="success_message" class="form-control" value="{{ old('success_message', $form->success_message ?? 'Thanks for signing up!') }}">
        </div>

        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $form->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active" style="font-size:13px;">Active (public link works)</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">{{ $form ? 'Save Changes' : 'Create Form' }}</button>
    <a href="{{ route('lead-forms.index') }}" class="btn btn-outline-primary">Cancel</a>
</div>
