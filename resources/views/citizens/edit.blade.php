@extends('layouts.app')

@section('title', 'Edit ' . $citizen->name . ' | Lupon')
@section('page-title', 'Citizens — Edit Profile')

@section('content')
<div style="padding: 1.5rem 2rem;">
    <div style="max-width: 640px; margin: 0 auto;">

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <div>
                <h2 style="font-size: 1.375rem; font-weight: 700; color: var(--text-primary);">
                    Edit <span style="color: var(--accent-blue);">{{ $citizen->name }}</span>
                </h2>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.2rem;">Update citizen profile information.</p>
            </div>
            <a href="{{ route('citizens.show', $citizen) }}" class="btn" style="background: var(--bg-card); border: 1px solid var(--border-light); color: var(--text-secondary);">
                <i class="ph ph-x"></i> Cancel
            </a>
        </div>

        <div class="card" style="padding: 1.5rem;">
            <form method="POST" action="{{ route('citizens.update', $citizen) }}">
                @csrf
                @method('PUT')
                @include('citizens._form')
                <div style="display: flex; justify-content: flex-end; align-items: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--border-color);">
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="{{ route('citizens.show', $citizen) }}" class="btn" style="background: var(--bg-card); border: 1px solid var(--border-light); color: var(--text-secondary);">
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="ph ph-floppy-disk"></i> Save Changes
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection

