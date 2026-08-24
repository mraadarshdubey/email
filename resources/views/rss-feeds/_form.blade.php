<div class="card">
    <div class="card-body" style="padding:24px;">
        <div class="mb-3">
            <label class="form-label">Feed Name</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $feed->name ?? '') }}" placeholder="e.g. Company Blog" required>
            @error('name')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Feed URL (RSS or Atom)</label>
            <input type="url" name="feed_url" class="form-control" value="{{ old('feed_url', $feed->feed_url ?? '') }}" placeholder="https://example.com/feed.xml" required>
            @error('feed_url')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label">Send digest to contacts tagged</label>
                <select name="recipient_tag_id" class="form-select" required>
                    <option value="">Select a tag</option>
                    @foreach($tags as $tag)
                        <option value="{{ $tag->id }}" {{ old('recipient_tag_id', $feed->recipient_tag_id ?? '') == $tag->id ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
                @error('recipient_tag_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
                @if($tags->isEmpty())
                    <div style="font-size:12px;color:#d97706;margin-top:4px;">No tags yet — <a href="{{ route('tags.create') }}">create one</a> first.</div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label">Send from account</label>
                <select name="email_account_id" class="form-select" required>
                    <option value="">Select an account</option>
                    @foreach($emailAccounts as $account)
                        <option value="{{ $account->id }}" {{ old('email_account_id', $feed->email_account_id ?? '') == $account->id ? 'selected' : '' }}>{{ $account->email }}</option>
                    @endforeach
                </select>
                @error('email_account_id')<div class="text-danger" style="font-size:12px;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-check form-switch mt-3">
            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $feed->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active" style="font-size:13px;">Active</label>
        </div>
    </div>
</div>

<div class="d-flex gap-2 mt-3">
    <button type="submit" class="btn btn-primary">{{ isset($feed) ? 'Save Changes' : 'Add Feed' }}</button>
    <a href="{{ route('rss-feeds.index') }}" class="btn btn-outline-primary">Cancel</a>
</div>
