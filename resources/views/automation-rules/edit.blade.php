@extends('layouts.app')

@section('title', 'Edit Rule - Sendpeak')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('home') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('automation-rules.index') }}">Rules</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="mb-4">
    <h1 style="font-size:20px;font-weight:600;margin:0;letter-spacing:-0.3px;">Edit Rule</h1>
    <p style="font-size:13px;color:#64748b;margin:2px 0 0;">{{ $rule->name }}</p>
</div>

<form action="{{ route('automation-rules.update', $rule) }}" method="POST" style="max-width:720px;">
    @csrf
    @method('PUT')
    @include('automation-rules._form')
</form>
@endsection
