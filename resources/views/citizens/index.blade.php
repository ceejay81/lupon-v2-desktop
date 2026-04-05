@extends('layouts.app')

@section('title', 'Citizens | Lupon')
@section('page-title', 'Citizen Registry')

@section('content')
<div style="padding: 1.5rem 2rem;">

    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <p style="color: var(--text-muted); font-size: 0.875rem;">
            {{ $citizens->total() }} registered citizen{{ $citizens->total() !== 1 ? 's' : '' }}
        </p>
        <a href="{{ route('citizens.create') }}" class="btn btn-primary">
            <i class="ph ph-user-plus"></i> Register Citizen
        </a>
    </div>

    {{-- Citizens Table --}}
    <div class="card" style="overflow: hidden;">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Purok</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th style="text-align: center;">Complainant</th>
                    <th style="text-align: center;">Respondent</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: right;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($citizens as $citizen)
                    @php $total = $citizen->cases_as_complainant_count + $citizen->cases_as_respondent_count; @endphp
                    <tr>
                        <td>
                            <a href="{{ route('citizens.show', $citizen) }}"
                               style="font-weight: 700; color: var(--accent-blue); text-decoration: none;">
                                {{ $citizen->name }}
                            </a>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--text-secondary);">{{ $citizen->purok ?? '—' }}</td>
                        <td style="font-size: 0.8rem; color: var(--text-secondary); max-width: 200px;">{{ Str::limit($citizen->address ?? '', 40) ?: '—' }}</td>
                        <td style="font-size: 0.8rem; color: var(--text-secondary);">{{ $citizen->phone ?? '—' }}</td>
                        <td style="text-align: center; font-size: 0.78rem; font-weight: 700; color: {{ $citizen->cases_as_complainant_count > 0 ? 'var(--accent-blue)' : 'var(--text-muted)' }};">
                            {{ $citizen->cases_as_complainant_count }}
                        </td>
                        <td style="text-align: center; font-size: 0.78rem; font-weight: 700; color: {{ $citizen->cases_as_respondent_count > 0 ? 'var(--danger)' : 'var(--text-muted)' }};">
                            {{ $citizen->cases_as_respondent_count }}
                        </td>
                        <td style="text-align: center; font-size: 0.78rem; font-weight: 700; color: {{ $total > 0 ? 'var(--text-primary)' : 'var(--text-muted)' }};">
                            {{ $total }}
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('citizens.show', $citizen) }}" class="btn-sm">
                                <i class="ph ph-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="padding: 3rem; text-align: center; color: var(--text-muted);">
                            <i class="ph ph-users" style="font-size: 2.5rem; display: block; margin-bottom: 0.5rem;"></i>
                            <p style="font-weight: 500;">No citizens registered yet.</p>
                            <p style="font-size: 0.8rem; margin-top: 0.25rem;">Citizens are added when cases are filed, or you can register them manually.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--border-light);">
            {{ $citizens->links('pagination::bootstrap-5') }}
        </div>
    </div>

</div>
@endsection
