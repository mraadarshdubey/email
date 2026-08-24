@extends('layouts.app')

@section('title', 'New Follow-up - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('email-followups.index') }}">Follow-ups</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<div class="mb-4">
    <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">New Follow-up</h1>
    <p style="font-size:13px;color:#64748b;margin:2px 0 0;">Automatically resend to whoever hasn't opened or clicked</p>
</div>

<form action="{{ route('email-followups.store') }}" method="POST" style="max-width:720px;">
    @csrf
    @include('email-followups._form')
</form>
@endsection
