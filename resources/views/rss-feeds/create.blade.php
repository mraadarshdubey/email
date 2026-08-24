@extends('layouts.app')

@section('title', 'Add RSS Feed - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rss-feeds.index') }}">RSS Feeds</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<div class="mb-4">
    <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Add RSS Feed</h1>
    <p style="font-size:13px;color:#64748b;margin:2px 0 0;">New posts will automatically go out as a digest email</p>
</div>

<form action="{{ route('rss-feeds.store') }}" method="POST" style="max-width:640px;">
    @csrf
    @include('rss-feeds._form')
</form>
@endsection
