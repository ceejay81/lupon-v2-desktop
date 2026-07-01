@extends('layouts.app')

@section('title', $citizen->name . ' | Citizens | Lupon')
@section('page-title', 'Citizen Profile')

@section('content')
<div style="padding: 1.5rem 2rem;">




    {{-- Header --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.5rem;">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <div style="width: 56px; height: 56px; border-radius: 50%; background: var(--accent-light); border: 2px solid var(--accent-blue); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; font-weight: 800; color: var(--accent-blue); flex-shrink: 0;">
                {{ strtoupper(substr($citizen->name, 0, 2)) }}
            </div>
            <div>
                <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary);">{{ $citizen->name }}</h2>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.15rem;">
                    @if($citizen->purok)<i class="ph ph-map-pin"></i> {{ $citizen->purok }} &nbsp;·&nbsp; @endif
                    @if($citizen->phone)<i class="ph ph-phone"></i> {{ $citizen->phone }} &nbsp;·&nbsp; @endif
                    Registered {{ $citizen->created_at->format('M d, Y') }}
                </p>
            </div>
        </div>
        <div style="display: flex; gap: 0.5rem; align-items: center;">
            <a href="{{ route('citizens.edit', $citizen) }}" class="btn" style="background: var(--bg-card); border: 1px solid var(--border-light); color: var(--text-secondary);">
                <i class="ph ph-pencil"></i> Edit
            </a>
            <form action="{{ route('citizens.destroy', $citizen) }}" method="POST" onsubmit="return confirm('WARNING: Are you sure you want to permanently delete this citizen? This will remove their profile entirely.')" style="margin:0;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn" style="background: var(--danger-light); border: 1px solid var(--danger); color: var(--danger); cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='var(--danger)'; this.style.color='white'" onmouseout="this.style.background='var(--danger-light)'; this.style.color='var(--danger)'">
                    <i class="ph ph-trash"></i> Delete
                </button>
            </form>
            <a href="{{ route('citizens.index') }}" class="btn" style="background: var(--bg-card); border: 1px solid var(--border-light); color: var(--text-secondary);">
                <i class="ph ph-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 1.5rem; align-items: start;">

        {{-- LEFT: Profile Card --}}
        <div style="display: flex; flex-direction: column; gap: 1.25rem;">

            <div class="card" style="padding: 1.5rem;">
                <p style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 1rem;">
                    <i class="ph ph-identification-card"></i> Profile
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.875rem;">
                    <div>
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.2rem;">Full Name</p>
                        <p style="font-size: 0.875rem; font-weight: 600; color: var(--text-primary);">{{ $citizen->name }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.2rem;">Purok</p>
                        <p style="font-size: 0.875rem; color: var(--text-secondary);">{{ $citizen->purok ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.2rem;">Address</p>
                        <p style="font-size: 0.875rem; color: var(--text-secondary);">{{ $citizen->address ?? '—' }}</p>
                    </div>
                    <div>
                        <p style="font-size: 0.72rem; color: var(--text-muted); margin-bottom: 0.2rem;">Phone</p>
                        <p style="font-size: 0.875rem; color: var(--text-secondary);">{{ $citizen->phone ?? '—' }}</p>
                    </div>
                </div>
            </div>

            {{-- Stats Card --}}
            @php
                $totalComplainant = $citizen->casesAsComplainant->count();
                $totalRespondent  = $citizen->casesAsRespondent->count();
                $total            = $totalComplainant + $totalRespondent;
                $settled          = $citizen->casesAsComplainant->where('status', 'settled')->count()
                                  + $citizen->casesAsRespondent->where('status', 'settled')->count();
                $active           = $citizen->casesAsComplainant->whereNotIn('status', ['settled','dismissed','withdrawal','archived'])->count()
                                  + $citizen->casesAsRespondent->whereNotIn('status', ['settled','dismissed','withdrawal','archived'])->count();
            @endphp
            <div class="card" style="padding: 1.5rem;">
                <p style="font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 1rem;">
                    <i class="ph ph-chart-bar"></i> Case Summary
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0.875rem; background: var(--bg-hover); border-radius: var(--radius-md);">
                        <span style="font-size: 0.8rem; color: var(--text-secondary);">Total Cases</span>
                        <span style="font-size: 1rem; font-weight: 800; color: var(--text-primary);">{{ $total }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0.875rem; background: #1e293b; border-radius: var(--radius-md);">
                        <span style="font-size: 0.8rem; color: var(--accent-blue);">As Complainant</span>
                        <span style="font-size: 1rem; font-weight: 800; color: var(--accent-blue);">{{ $totalComplainant }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0.875rem; background: var(--danger-light); border-radius: var(--radius-md);">
                        <span style="font-size: 0.8rem; color: var(--danger);">As Respondent</span>
                        <span style="font-size: 1rem; font-weight: 800; color: var(--danger);">{{ $totalRespondent }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0.875rem; background: var(--success-light); border-radius: var(--radius-md);">
                        <span style="font-size: 0.8rem; color: var(--success);">Settled</span>
                        <span style="font-size: 1rem; font-weight: 800; color: var(--success);">{{ $settled }}</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0.875rem; background: var(--warning-light); border-radius: var(--radius-md);">
                        <span style="font-size: 0.8rem; color: var(--warning);">Active</span>
                        <span style="font-size: 1rem; font-weight: 800; color: var(--warning);">{{ $active }}</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- RIGHT: Cases --}}
        <div style="display: flex; flex-direction: column; gap: 1.5rem;">

            {{-- Cases as Complainant --}}
            <div class="card" style="overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph ph-user-circle" style="color: var(--accent-blue); font-size: 1.1rem;"></i>
                    <p style="font-size: 0.8rem; font-weight: 700; color: var(--text-primary);">Cases as Complainant</p>
                    <span style="margin-left: auto; font-size: 0.72rem; font-weight: 700; background: var(--accent-light); color: var(--accent-blue); padding: 2px 8px; border-radius: var(--radius-full);">
                        {{ $totalComplainant }}
                    </span>
                </div>
                @if($citizen->casesAsComplainant->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Respondent</th>
                                <th>Nature</th>
                                <th>Status</th>
                                <th>Filed</th>
                                <th>Next Hearing</th>
                                <th style="text-align: right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($citizen->casesAsComplainant as $case)
                                @php $nextH = $case->hearings->first(); @endphp
                                <tr>
                                    <td><span style="font-weight: 700; color: var(--accent-blue);">{{ $case->case_number }}</span></td>
                                    <td style="font-size: 0.8rem;">{{ $case->respondent }}</td>
                                    <td style="font-size: 0.78rem; color: var(--text-secondary);">
                                        {{ $case->nature_of_case }}
                                    </td>
                                    <td><span class="status-badge {{ $case->status_badge_class }}">{{ $case->status_label }}</span></td>
                                    <td style="font-size: 0.78rem; color: var(--text-muted);">{{ $case->filed_date->format('M d, Y') }}</td>
                                    <td style="font-size: 0.78rem;">
                                        @if($nextH)
                                            <span style="color: var(--success); font-weight: 600;">
                                                <i class="ph ph-calendar-check"></i> {{ $nextH->scheduled_at->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span style="color: var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('cases.show', $case) }}"
                                           style="font-size: 0.75rem; color: var(--accent-blue); text-decoration: none; font-weight: 600;">
                                            View <i class="ph ph-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                        <i class="ph ph-folder-open" style="font-size: 1.75rem; display: block; margin-bottom: 0.4rem;"></i>
                        <p style="font-size: 0.8rem;">No cases filed as complainant.</p>
                    </div>
                @endif
            </div>

            {{-- Cases as Respondent --}}
            <div class="card" style="overflow: hidden;">
                <div style="padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; gap: 0.5rem;">
                    <i class="ph ph-user-circle-minus" style="color: var(--danger); font-size: 1.1rem;"></i>
                    <p style="font-size: 0.8rem; font-weight: 700; color: var(--text-primary);">Cases as Respondent</p>
                    <span style="margin-left: auto; font-size: 0.72rem; font-weight: 700; background: var(--danger-light); color: var(--danger); padding: 2px 8px; border-radius: var(--radius-full);">
                        {{ $totalRespondent }}
                    </span>
                </div>
                @if($citizen->casesAsRespondent->count())
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Case No.</th>
                                <th>Complainant</th>
                                <th>Nature</th>
                                <th>Status</th>
                                <th>Filed</th>
                                <th>Next Hearing</th>
                                <th style="text-align: right;"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($citizen->casesAsRespondent as $case)
                                @php $nextH = $case->hearings->first(); @endphp
                                <tr>
                                    <td><span style="font-weight: 700; color: var(--accent-blue);">{{ $case->case_number }}</span></td>
                                    <td style="font-size: 0.8rem;">{{ $case->complainant }}</td>
                                    <td style="font-size: 0.78rem; color: var(--text-secondary);">
                                        {{ $case->nature_of_case }}
                                    </td>
                                    <td><span class="status-badge {{ $case->status_badge_class }}">{{ $case->status_label }}</span></td>
                                    <td style="font-size: 0.78rem; color: var(--text-muted);">{{ $case->filed_date->format('M d, Y') }}</td>
                                    <td style="font-size: 0.78rem;">
                                        @if($nextH)
                                            <span style="color: var(--success); font-weight: 600;">
                                                <i class="ph ph-calendar-check"></i> {{ $nextH->scheduled_at->format('M d, Y') }}
                                            </span>
                                        @else
                                            <span style="color: var(--text-muted);">—</span>
                                        @endif
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="{{ route('cases.show', $case) }}"
                                           style="font-size: 0.75rem; color: var(--accent-blue); text-decoration: none; font-weight: 600;">
                                            View <i class="ph ph-arrow-right"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div style="padding: 2rem; text-align: center; color: var(--text-muted);">
                        <i class="ph ph-folder-open" style="font-size: 1.75rem; display: block; margin-bottom: 0.4rem;"></i>
                        <p style="font-size: 0.8rem;">No cases filed as respondent.</p>
                    </div>
                @endif
            </div>

        </div>
    </div>

</div>
@endsection

