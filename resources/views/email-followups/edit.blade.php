@extends('layouts.app')

@section('title', 'Edit Follow-up - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-followups.index') }}">Follow-ups</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="mb-4">
    <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Edit Follow-up</h1>
    <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $followup->name }}</p>
</div>

<form action="{{ route('email-followups.update', $followup) }}" method="POST" style="max-width:720px;">
    @csrf
    @method('PUT')
    @include('email-followups._form')
</form>
@endsection
