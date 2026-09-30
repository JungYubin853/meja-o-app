<x-app-layout>
    <div class="min-h-[calc(100vh-64px)] flex flex-col p-4 sm:p-6 lg:p-8">
        
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight">Calendar</h2>
                <p class="text-sm text-slate-500 font-medium">Public Holidays & Collective Leave Days</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-soft-xs border border-slate-200">
                    <span class="w-3 h-3 rounded-full bg-red-500"></span>
                    National Holiday
                </div>
                <div class="flex items-center gap-1.5 text-xs font-bold text-slate-600 bg-white px-3 py-1.5 rounded-lg shadow-soft-xs border border-slate-200">
                    <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                    Collective Leave
                </div>
            </div>
        </div>

        <div class="flex-1 bg-white rounded-2xl shadow-soft-sm border border-slate-200/80 p-5">
            <div id="calendar" class="w-full"></div>
        </div>
    </div>

    <!-- FullCalendar CSS and JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <style>
        /* Custom styling for FullCalendar to match Meja-O theme */
        .fc-theme-standard td, .fc-theme-standard th {
            border-color: #e2e8f0;
        }
        .fc-col-header-cell {
            padding: 12px 0 !important;
            background-color: #f8fafc;
            color: #475569;
            font-size: 0.875rem;
            text-transform: uppercase;
            font-weight: 700;
        }
        .fc .fc-toolbar-title {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.25rem;
        }
        .fc .fc-button-primary {
            background-color: #ffffff !important;
            border-color: #e2e8f0 !important;
            color: #475569 !important;
            font-weight: 700 !important;
            text-transform: capitalize !important;
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            transition: all 0.2s;
        }
        .fc .fc-button-primary:hover {
            background-color: #f8fafc !important;
            color: #0f172a !important;
        }
        .fc .fc-button-primary:not(:disabled):active,
        .fc .fc-button-primary:not(:disabled).fc-button-active {
            background-color: #f1f5f9 !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
            box-shadow: inset 0 2px 4px 0 rgb(0 0 0 / 0.05) !important;
        }
        .fc-event {
            border-radius: 6px !important;
            padding: 2px 4px;
            font-size: 0.75rem;
            font-weight: 600;
            border: none !important;
            margin-bottom: 2px !important;
        }
        .fc-day-today {
            background-color: #fefce8 !important; /* light yellow for today */
        }
        .fc-daygrid-day-number {
            font-weight: 600;
            color: #334155;
            padding: 8px !important;
        }
    
        /* Year view (multiMonth) specific styling */
        .fc .fc-multimonth-daygrid {
            background-color: #ffffff;
        }
        .fc .fc-multimonth-title {
            font-size: 1.1rem;
            font-weight: 800;
            color: #334155;
            padding: 12px 0;
            text-align: center;
        }
        /* Make dots slightly bigger in year view */
        .fc-daygrid-event-dot {
            border-width: 4px !important;
        }


        /* Reduce padding in multi-month year view day headers to prevent scrolling */
        .fc-multimonth-daygrid .fc-col-header-cell {
            padding: 4px 0 !important;
            font-size: 0.75rem;
        }
        .fc-multimonth-daygrid .fc-daygrid-day-number {
            padding: 4px !important;
            font-size: 0.75rem;
        }
        .fc .fc-multimonth-title {
            padding: 8px 0;
            font-size: 1rem;
        }

        /* Force events to act as backgrounds in multiMonthYear view */
        .fc-multimonth-daygrid .fc-daygrid-day-frame {
            position: relative;
        }
        .fc-multimonth-daygrid .fc-daygrid-event-harness {
            position: absolute !important;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
        }
        .fc-multimonth-daygrid .fc-daygrid-event {
            display: block !important;
            width: 100%;
            height: 100%;
            border-radius: 0 !important;
            opacity: 0.25; /* Light color */
            background-color: var(--fc-event-bg-color, #3788d8) !important;
        }
        .fc-multimonth-daygrid .fc-daygrid-event-dot {
            display: none !important;
        }
        .fc-multimonth-daygrid .fc-event-title {
            display: none !important;
        }
        /* Completely hide any +1 more links */
        .fc-multimonth-daygrid .fc-more-link {
            display: none !important;
        }
        .fc-multimonth-daygrid .fc-daygrid-more-link {
            display: none !important;
        }
        /* Ensure the day number stays on top */
        .fc-multimonth-daygrid .fc-daygrid-day-top {
            position: relative;
            z-index: 10;
        }
</style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'multiMonthYear',
                buttonText: {
                    multiMonthYear: 'Year',
                    dayGridMonth: 'Month',
                    timeGridWeek: 'Week'
                },
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'multiMonthYear,dayGridMonth,timeGridWeek'
                },
                
                multiMonthMaxColumns: 6,
                multiMonthMinWidth: 120,
                events: '/api/calendar/holidays',
                height: 'auto',
                firstDay: 1, // Start on Monday
                navLinks: true, // can click day/week names to navigate views

                  navLinkDayClick: 'timeGridWeek', // clicking a date goes to week view
                  views: {
                      multiMonthYear: {
                          dayHeaderFormat: { weekday: 'narrow' },
                          eventDisplay: 'background',
                          dayMaxEvents: false
                      }
                  },
                dayMaxEvents: true, // allow "more" link when too many events
                eventTimeFormat: {
                    hour: 'numeric',
                    minute: '2-digit',
                    meridiem: 'short'
                },
                eventDidMount: function(info) {
                    // Tooltip functionality could go here
                    info.el.title = info.event.title;
                }
            });
            calendar.render();
        });
    </script>
</x-app-layout>
