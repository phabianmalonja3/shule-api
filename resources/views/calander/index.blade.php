<x-layout>
    <x-slot:title>
        Parent Payments
    </x-slot:title>
    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="container">
                <div class="card">
                    <h3 class="p-3 card-header">School Calendar</h3>
                    <div class="card-body">
                        <div id="calendar"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>

<script src="{{ asset('assets/bundles/izitoast/js/iziToast.min.js') }}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        var SITEURL = "{{ url('/') }}";
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var calendar = $('#calendar').fullCalendar({
            height: 'auto',
  defaultView: 'month',
  editable: true,
  selectable: true,
  header: {
    left: 'prev,next today',
    center: 'title',
    right: 'month,agendaWeek,agendaDay,listMonth'
  },
    editable: true,
    events: SITEURL + "/fullcalender",
    displayEventTime: true, // Make sure to show event time
    eventRender: function (event, element, view) {
        if (event.allDay === 'true') {
            event.allDay = true;
        } else {
            event.allDay = false;
        }
    },
    selectable: true,
    selectHelper: true,
    select: function (start, end, allDay) {
        var title = prompt('Event Title:');
        if (title) {
            var start = $.fullCalendar.formatDate(start, "YYYY-MM-DD HH:mm:ss"); // Ensure full datetime
            var end = $.fullCalendar.formatDate(end, "YYYY-MM-DD HH:mm:ss"); // Same here
            $.ajax({
                url: SITEURL + "/fullcalenderAjax",
                data: {
                    title: title,
                    start: start,
                    end: end,
                    type: 'add'
                },
                type: "POST",
                success: function (data) {
                    displayMessage("Event Created Successfully");

                    calendar.fullCalendar('renderEvent', {
                        id: data.id,
                        title: title,
                        start: start,
                        end: end,
                        allDay: allDay
                    }, true);

                    calendar.fullCalendar('unselect');
                }
            });
        }
    },
    eventDrop: function (event, delta) {
        var start = $.fullCalendar.formatDate(event.start, "YYYY-MM-DD HH:mm:ss"); // Ensure full datetime
        var end = $.fullCalendar.formatDate(event.end, "YYYY-MM-DD HH:mm:ss"); // Same here

        $.ajax({
            url: SITEURL + '/fullcalenderAjax',
            data: {
                title: event.title,
                start: start,
                end: end,
                id: event.id,
                type: 'update'
            },
            type: "POST",
            success: function (response) {
                displayMessage("Event Updated Successfully");
            }
        });
    },
    eventClick: function (event) {
        var deleteMsg = confirm("Do you really want to delete?");
        if (deleteMsg) {
            $.ajax({
                type: "POST",
                url: SITEURL + '/fullcalenderAjax',
                data: {
                    id: event.id,
                    type: 'delete'
                },
                success: function (response) {
                    calendar.fullCalendar('removeEvents', event.id);
                    displayMessage("Event Deleted Successfully");
                }
            });
        }
    }
});

    });

    function displayMessage(message) {
        iziToast.success({
    title: '',
    message: `${message}`,
    position: 'topRight'
    
  });
        // toastr.success(message, 'Event');
    }
</script>
