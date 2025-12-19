<x-layout>
    <x-slot:title>
        Weekly Attendance Report
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
                                <h4>Generate Weekly Attendance Report</h4>
                            </div>
                            <div class="card-body">
                                <form action="{{ route('attendance.weekly.report') }}" method="GET">
                                    <div class="form-group">
                                        <label for="stream">Stream:</label>
                                        <select class="form-control" name="stream" id="stream" required>
                                            @foreach ($streams as $stream)
                                                <option 
                                                    value="{{ $stream->id }}" 
                                                    {{ request('stream', $streams->first()->id) == $stream->id ? 'selected' : '' }}>
                                                   Class {{ \Str::replaceFirst('Form ' ?? 'Class ', '', $stream->schoolClass->name) }} {{ $stream->alias }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                
                                    <div class="form-group">
                                        <label for="start_date">Start Date:</label>
                                        <input type="date" class="form-control" name="start_date" id="start_date" value="{{ $startDate->format('Y-m-d') }}" required>
                                    </div>
                                
                                    <div class="form-group">
                                        <label for="end_date">End Date:</label>
                                        <input type="date" class="form-control" name="end_date" id="end_date" value="{{ $endDate->format('Y-m-d') }}" readonly>
                                    </div>
                                
                                    <button type="submit" class="btn btn-primary">Generate Report</button>
                                </form>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
   


</x-layout>

<script>
    $(document).ready(function() {
      $('#start_date').on('change', function() {
        const startDate = new Date($(this).val());
    
        // Check if a valid date is selected
        if (isNaN(startDate.getTime())) {
          $('#end_date').val('');
          return;
        }
    
        // Calculate the ending date (6 days after the start date)
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 6);
    
        // Format the ending date as YYYY-MM-DD
        $('#end_date').val(endDate.toISOString().split('T')[0]);
      });
    });
        </script>