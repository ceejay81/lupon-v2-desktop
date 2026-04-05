@extends('layouts.app')

@section('title', 'Edit Case ' . $case->case_number . ' | Lupon')
@section('page-title', 'Digital Blotter - Edit Case')

@section('content')
    <div style="padding: 1.5rem;">
        @livewire('case-edit-form', ['case' => $case])
    </div>
@endsection
