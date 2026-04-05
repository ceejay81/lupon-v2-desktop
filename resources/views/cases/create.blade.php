@extends('layouts.app')

@section('title', 'New Case | Lupon')
@section('page-title', 'Digital Blotter - File New Case')

@section('content')
    <div style="padding: 1.5rem;">
        @livewire('case-form')
    </div>
@endsection
