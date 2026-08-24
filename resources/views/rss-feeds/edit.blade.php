@extends('layouts.app')

@section('title', 'Edit RSS Feed - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rss-feeds.index') }}">RSS Feeds</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="mb-4">
    <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Edit RSS Feed</h1>
    <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $feed->name }}</p>
</div>

<form action="{{ route('rss-feeds.update', $feed) }}" method="POST" style="max-width:640px;">
    @csrf
    @method('PUT')
    @include('rss-feeds._form')
</form>
@endsection
