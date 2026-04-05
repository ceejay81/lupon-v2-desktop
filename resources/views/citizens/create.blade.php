@extends('layouts.app')

@section('title', 'Register Citizen | Lupon')
@section('page-title', 'Citizens — Register New')

@section('content')
<div style="padding: 1.5rem 2rem;">
    <div style="max-width: 640px; margin: 0 auto;">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.375rem; font-weight: 700; color: var(--text-primary);">Register Citizen</h2>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">Add a new citizen to the registry.</p>
            </div>
            <a href="{{ route('citizens.index') }}" class="btn" style="background: var(--bg-card); border: 1px solid var(--border-light); color: var(--text-secondary);">
                <i class="ph ph-arrow-left"></i> Back
            </a>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <form method="POST" action="{{ route('citizens.store') }}">
                @csrf
                @include('citizens._form')
                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <a href="{{ route('citizens.index') }}" class="btn" style="background: var(--bg-card); border: 1px solid var(--border-light); color: var(--text-secondary);">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="ph ph-user-plus"></i> Register Citizen
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

