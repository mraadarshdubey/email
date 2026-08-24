@extends('layouts.app')

@section('title', 'New Form - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('lead-forms.index') }}">Forms</a></li>
    <li class="breadcrumb-item active">New</li>
@endsection

@section('content')
<div class="mb-4">
    <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">New Form</h1>
    <p style="font-size:13px;color:#64748b;margin:2px 0 0;">A public link anyone can fill in, wired straight into your automations</p>
</div>

<form action="{{ route('lead-forms.store') }}" method="POST" style="max-width:640px;">
    @csrf
    @include('lead-forms._form')
</form>
@endsection
