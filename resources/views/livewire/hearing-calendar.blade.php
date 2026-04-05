<div>
    {{-- Hearing Form Modal Moved to Index --}}

    {{-- Calendar Toolbar --}}
    <div style="padding: 1.5rem 2rem; border-bottom: 1px solid var(--border-color); background: var(--bg-card); display: flex; justify-content: space-between; align-items: center;">
        <div style="display: flex; gap: 0.5rem; background: var(--bg-page); padding: 4px; border-radius: 10px; border: 1px solid var(--border-color);">
            <button wire:click="changeView('dayGridMonth')" 
                    style="padding: 8px 16px; border: none; background: {{ $view === 'dayGridMonth' ? 'var(--accent-blue)' : 'transparent' }}; color: {{ $view === 'dayGridMonth' ? 'white' : 'var(--text-secondary)' }}; font-weight: 700; font-size: 0.75rem; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                Month View
            </button>
            <button wire:click="changeView('listWeek')" 
                    style="padding: 8px 16px; border: none; background: {{ $view === 'listWeek' ? 'var(--accent-blue)' : 'transparent' }}; color: {{ $view === 'listWeek' ? 'white' : 'var(--text-secondary)' }}; font-weight: 700; font-size: 0.75rem; border-radius: 6px; cursor: pointer; transition: all 0.2s;">
                Weekly Agenda
            </button>
        </div>
        
        <div style="display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;">
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #3b82f6;"></div> Scheduled
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #10b981;"></div> Confirmed
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #8b5cf6;"></div> Postponed
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #f59e0b;"></div> Failed
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.75rem; font-weight: 700; color: var(--text-muted);">
                <div style="width: 10px; height: 10px; border-radius: 50%; background: #ef4444;"></div> Cancelled
            </div>
        </div>
    </div>

    {{-- Calendar Container --}}
    <div style="padding: 2rem;">
        <div id="hearing-calendar" wire:ignore style="background: var(--bg-card); border-radius: 12px; border: 1px solid var(--border-color);"></div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            initializeCalendar();

            function initializeCalendar() {
                const events = @json($this->events);
                const view = @json($this->view);
                if (typeof window.initHearingCalendar === 'function') {
                    window.initHearingCalendar(events, view);
                }
            }

            if (window.Livewire) {
                Livewire.on('viewChanged', () => {
                    setTimeout(() => {
                        const view = @json($this->view);
                        if (typeof window.changeCalendarView === 'function') {
                            window.changeCalendarView(view);
                        }
                    }, 100);
                });

                // hearingSaved is handled server-side via calendarRefresh event
                Livewire.on('calendarRefresh', ({ events }) => {
                    if (typeof window.refreshCalendarEvents === 'function' && events) {
                        window.refreshCalendarEvents(events);
                    }
                });
            }
        });
    </script>
    @endpush

    <style>
        #hearing-calendar .fc { font-family: inherit; }
        #hearing-calendar .fc-toolbar-title { font-size: 1.25rem !important; font-weight: 800 !important; color: var(--text-primary) !important; }
        #hearing-calendar .fc-button { background: var(--bg-page) !important; border: 1px solid var(--border-color) !important; color: var(--text-secondary) !important; font-weight: 700 !important; border-radius: 10px !important; }
        #hearing-calendar .fc-button-active { background: var(--accent-blue) !important; color: white !important; border-color: var(--accent-blue) !important; }
        #hearing-calendar .fc-event { border-radius: 6px !important; padding: 2px 6px !important; border: none !important; font-weight: 600 !important; }
        #hearing-calendar .fc-day-today { background: var(--accent-light) !important; }
        #hearing-calendar .fc-col-header-cell { background: var(--bg-page) !important; padding: 12px 0 !important; font-weight: 800 !important; text-transform: uppercase; font-size: 0.65rem; color: var(--text-muted); }
    </style>
</div>