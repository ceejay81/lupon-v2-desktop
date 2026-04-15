import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';
import listPlugin from '@fullcalendar/list';

window.initHearingCalendar = function(events, initialView = 'dayGridMonth') {
    const calendarEl = document.getElementById('hearing-calendar');
    
    if (!calendarEl) return;
    
    // Destroy existing calendar if it exists
    if (window.hearingCalendar) {
        window.hearingCalendar.destroy();
    }

    window.hearingCalendar = new Calendar(calendarEl, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin, listPlugin],
        initialView: initialView,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: ''
        },
        events: events,
        timeZone: 'local',
        height: 'auto',
        eventClick: function(info) {
            window.location.href = info.event.extendedProps.url;
        },
        eventMouseEnter: function(info) {
            const tooltip = document.createElement('div');
            tooltip.className = 'calendar-tooltip';
            tooltip.innerHTML = `
                <div style="background: var(--bg-card); border: 1px solid var(--border-medium); border-radius: var(--radius-md); padding: 1.25rem; box-shadow: var(--shadow-lg); font-size: 0.875rem; max-width: 300px; z-index: 1000; color: var(--text-primary);">
                    <div style="font-weight: 800; color: var(--accent-blue); margin-bottom: 0.75rem; font-size: 1.125rem; line-height: 1.2;">${info.event.extendedProps.nature_of_case}</div>
                    <div style="margin-bottom: 0.625rem; display: flex; align-items: flex-start; gap: 0.5rem;"><span style="color: var(--text-muted); width: 20px;">📅</span> <span><strong>When:</strong> ${info.event.extendedProps.scheduled_time}</span></div>
                    <div style="margin-bottom: 0.625rem; display: flex; align-items: flex-start; gap: 0.5rem;"><span style="color: var(--text-muted); width: 20px;">📍</span> <span><strong>Where:</strong> ${info.event.extendedProps.location}</span></div>
                    <div style="margin-bottom: 0.625rem; display: flex; align-items: flex-start; gap: 0.5rem;"><span style="color: var(--text-muted); width: 20px;">⚖️</span> <span><strong>Type:</strong> ${info.event.extendedProps.hearing_type}</span></div>
                    <div style="margin-bottom: 0.625rem; display: flex; align-items: flex-start; gap: 0.5rem;"><span style="color: var(--text-muted); width: 20px;">📋</span> <span><strong>Status:</strong> ${info.event.extendedProps.status}</span></div>
                    <hr style="margin: 1rem 0; border: none; border-top: 1px solid var(--border-light);">
                    <div style="margin-bottom: 0.375rem; display: flex; align-items: center; gap: 0.5rem;"><i class="ph-bold ph-user" style="color: var(--accent-blue); font-size: 0.75rem;"></i> <strong>Complainant:</strong> ${info.event.extendedProps.complainant}</div>
                    <div style="margin-bottom: 0.375rem; display: flex; align-items: center; gap: 0.5rem;"><i class="ph-bold ph-user" style="color: var(--danger); font-size: 0.75rem;"></i> <strong>Respondent:</strong> ${info.event.extendedProps.respondent}</div>
                    <div style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-muted); font-weight: 600; background: var(--bg-hover); padding: 4px 8px; border-radius: 4px; display: inline-block;">Case: ${info.event.extendedProps.case_number}</div>
                </div>
            `;
            
            document.body.appendChild(tooltip);
            
            const rect = info.el.getBoundingClientRect();
            tooltip.style.position = 'absolute';
            tooltip.style.left = rect.left + 'px';
            tooltip.style.top = (rect.bottom + 5) + 'px';
            tooltip.style.zIndex = '1000';
            
            info.el.tooltip = tooltip;
        },
        eventMouseLeave: function(info) {
            if (info.el.tooltip) {
                document.body.removeChild(info.el.tooltip);
                info.el.tooltip = null;
            }
        },
    });

    window.hearingCalendar.render();
};

window.changeCalendarView = function(view) {
    if (window.hearingCalendar) {
        window.hearingCalendar.changeView(view);
    }
};

window.refreshCalendarEvents = function(events) {
    if (window.hearingCalendar) {
        window.hearingCalendar.removeAllEvents();
        window.hearingCalendar.addEventSource(events);
    }
};