@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
    <div class="rounded-xl border border-stone-200 bg-white p-8 shadow-sm">
        <p class="text-sm text-stone-500">Admin area</p>
        <h1 class="mt-2 text-2xl font-semibold tracking-tight">Welcome, {{ auth()->user()->name }}</h1>
        <p class="mt-3 text-sm text-stone-600">The administrative dashboard will be built here next.</p>
    </div>
@endsection
