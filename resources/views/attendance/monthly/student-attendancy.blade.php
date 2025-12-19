<x-layout>
  <x-slot:title>
      Take Daily Attendance
  </x-slot:title>

  <x-navbar />
  <x-admin.sidebar />

  <div class="main-content" style="min-height: 635px;">
      <section class="section">
          <div class="section-body">
              <div class="row">
                  <div class="col-12">
                      <div class="card">
                          <div class="card-header">
                              <h4>Daily Attendance</h4>  </div>
                          <div class="card-body">
                              <div id="attendance-datepicker"></div>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </section>
  </div>


  <style>
      /* Present: green */
      .present a {
          background-color: #4CAF50 !important;
          color: #fff !important;
          border-radius: 50%;
      }

      /* Absent: red */
      .absent a {
          background-color: #F44336 !important;
          color: #fff !important;
          border-radius: 50%;
      }

      /* Excused: yellow */
      .excuse a {
          background-color: #FFEB3B !important;
          color: #000 !important;
          border-radius: 50%;
      }
      .ui-datepicker td a { /* Target the date cells */
          padding: 5px; /* Adjust padding as needed */
          display: block; /* Make the entire cell clickable */
          text-align: center; /* Center the text */
      }

  </style>


  <script>
      $(function() {
          var attendanceData = {
              "{{ \Carbon\Carbon::today()->format('Y-m-d') }}": "present",
              "2025-03-10": "absent",
              "2025-03-12": "excuse",
              "2025-03-15": "present", // Example present entry
               "2025-03-17": "absent",  // Example absent entry
               "2025-03-20": "excuse"   // Example excused entry
          };

          $("#attendance-datepicker").datepicker({
              beforeShowDay: function(date) {
                  var formattedDate = $.datepicker.formatDate('yy-mm-dd', date);
                  var status = attendanceData[formattedDate];
                  if (status) {
                      return [true, status, status.charAt(0).toUpperCase() + status.slice(1)];
                  }
                  return [true, "", ""]; // No class or tooltip by default
              },
              // Add other datepicker options if needed, e.g., changeMonth, changeYear, etc.
              dateFormat: 'mm/dd/yy', // Example date format
              showOtherMonths: true,
              selectOtherMonths: true,
              dayNamesMin: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
              monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
              
          });
      });
  </script>
</x-layout>