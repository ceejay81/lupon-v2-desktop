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
                <div style="background: white; border: 1px solid var(--border-light); border-radius: var(--radius-md); padding: 1rem; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); font-size: 0.875rem; max-width: 280px; z-index: 1000;">
                    <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem; font-size: 1rem;">${info.event.extendedProps.nature_of_case}</div>
                    <div style="margin-bottom: 0.5rem;"><strong>📅 When:</strong> ${info.event.extendedProps.scheduled_time}</div>
                    <div style="margin-bottom: 0.5rem;"><strong>📍 Where:</strong> ${info.event.extendedProps.location}</div>
                    <div style="margin-bottom: 0.5rem;"><strong>⚖️ Type:</strong> ${info.event.extendedProps.hearing_type}</div>
                    <div style="margin-bottom: 0.5rem;"><strong>📋 Status:</strong> ${info.event.extendedProps.status}</div>
                    <hr style="margin: 0.75rem 0; border: none; border-top: 1px solid #e5e7eb;">
                    <div style="margin-bottom: 0.25rem;"><strong>👤 Complainant:</strong> ${info.event.extendedProps.complainant}</div>
                    <div style="margin-bottom: 0.25rem;"><strong>👤 Respondent:</strong> ${info.event.extendedProps.respondent}</div>
                    <div style="margin-top: 0.75rem; font-size: 0.75rem; color: #64748b;">Case: ${info.event.extendedProps.case_number}</div>
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