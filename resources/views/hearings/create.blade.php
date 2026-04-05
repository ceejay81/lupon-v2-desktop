@extends('layouts.app')

@section('title', 'Schedule Hearing | Lupon')
@section('page-title', 'Hearings — Schedule')

@section('content')
<div style="padding: 1.5rem 2rem;">
    @livewire('hearing-form', ['case_id' => request()->integer('case_id') ?: null], 'hearing-form-create')

    {{-- Auto-open the modal on page load --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.dispatch('openHearingForm', { caseId: {{ request()->integer('case_id') ?: 'null' }} });
        });
    </script>
</div>
@endsection
