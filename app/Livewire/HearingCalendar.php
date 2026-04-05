<?php

namespace App\Livewire;

use App\Models\Hearing;
use Livewire\Attributes\On;
use Livewire\Component;

class HearingCalendar extends Component
{
    public string $view = 'dayGridMonth';

    public string $currentDate = '';

    public function mount(): void
    {
        $this->currentDate = now()->format('Y-m-d');
    }

    public function getEventsProperty(): array
    {
        $hearings = Hearing::with(['luponCase'])
            ->whereYear('scheduled_at', now()->year)
            ->whereNotIn('status', ['completed']) // Exclude completed hearings
            ->get();

        return $hearings->map(function ($hearing) {
            $statusColor = match ($hearing->status) {
                'cancelled' => '#ef4444',
                'failed' => '#f59e0b',
                'postponed' => '#8b5cf6',
                'confirmed' => '#10b981',
                default => '#3b82f6', // scheduled
            };

            // Create user-friendly title with just names and time
            $complainant = explode(' ', $hearing->luponCase->complainant)[0]; // First name only
            $respondent = explode(' ', $hearing->luponCase->respondent)[0]; // First name only
            $time = $hearing->scheduled_at->format('g:i A'); // e.g., "2:30 PM"

            // Clean format: "John vs Mary - 2:30 PM"
            $title = "{$complainant} vs {$respondent} - {$time}";

            // Add status indicator for non-scheduled hearings
            if ($hearing->status !== 'scheduled') {
                $statusEmoji = match ($hearing->status) {
                    'cancelled' => '❌',
                    'failed' => '⚠️',
                    default => '',
                };
                $title = "{$statusEmoji} {$title}";
            }

            return [
                'id' => $hearing->id,
                'title' => $title,
                'start' => $hearing->scheduled_at->format('Y-m-d\TH:i:s'), // no timezone suffix — FullCalendar treats as local
                'backgroundColor' => $statusColor,
                'borderColor' => $statusColor,
                'extendedProps' => [
                    'case_number' => $hearing->luponCase->case_number,
                    'complainant' => $hearing->luponCase->complainant,
                    'respondent' => $hearing->luponCase->respondent,
                    'nature_of_case' => $hearing->luponCase->nature_of_case,
                    'location' => $hearing->location ?? 'N/A',
                    'status' => ucfirst($hearing->status),
                    'hearing_type' => ucfirst($hearing->hearing_type),
                    'scheduled_time' => $hearing->scheduled_at->format('F j, Y \a\t g:i A'),
                    'url' => route('hearings.show', $hearing),
                ],
            ];
        })->toArray();
    }

    #[On('hearingSaved')]
    #[On('hearingStatusUpdated')]
    public function refreshEvents(): void
    {
        $this->dispatch('calendarRefresh', events: $this->events);
    }

    public function changeView(string $view): void
    {
        // Only allow Month and Agenda views
        if (in_array($view, ['dayGridMonth', 'listWeek'])) {
            $this->view = $view;
            $this->dispatch('viewChanged');
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.hearing-calendar');
    }
}
