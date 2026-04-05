@extends('layouts.app')

@section('title', 'Digital Blotter | Lupon')
@section('page-title', 'Digital Blotter - Cases')

@section('content')
    <div style="padding: 1.5rem;">



        @livewire('case-list')
    </div>
@endsection
