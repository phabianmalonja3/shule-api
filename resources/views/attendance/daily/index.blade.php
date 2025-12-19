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
                                <h4>Manage Attendance - {{ $today->format('d F Y') }}</h4>
                            </div>

                            <div class="card-body">
                                <!-- Tabs Navigation -->
                                <ul class="nav nav-tabs" id="attendanceTabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="take-attendance-tab" data-toggle="tab" href="#take-attendance" role="tab">Take Attendance</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="attendance-records-tab" data-toggle="tab" href="#attendance-records" role="tab">Attendance Records</a>
                                    </li>
                                </ul>

                                <!-- Tabs Content -->
                                <div class="mt-3 tab-content">
                                    <!-- Take Attendance Tab -->
                                    <div class="tab-pane fade show active" id="take-attendance" role="tabpanel">
                                        <form id="streamFilterForm" action="" method="GET">
                                            <div class="mx-4 mt-3 mb-0 form-group">
                                                @if ($streams->count() > 1)
                                                    <label for="stream" class="mr-2">Filter by Stream:</label>
                                                    <select class="w-auto form-control d-inline-block" id="stream" name="stream" aria-label="Select Stream">
                                                        <option value="">Select Stream</option>
                                                        @foreach ($streams as $stream)
                                                            <option value="{{ $stream->id }}">
                                                                {{ \Str::replaceFirst('Form ' ?? 'Class ', '', $stream->schoolClass->name) }}
                                                                {{ $stream->alias }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <input type="hidden" name="stream" value="{{ request('stream', $streams->first()->id) }}">
                                                @endif
                                            </div>
                                        </form>

                                        @if (session('success'))
                                            <div class="alert alert-success">
                                                {{ session('success') }}
                                            </div>
                                        @endif

                                        <form id="attendanceForm" action="{{ route('attendance.daily.store', ['stream' => request('stream')]) }}" method="POST">
                                            @csrf
                                            <div class="form-group">
                                                <label for="date">Date:</label>
                                                <input type="date" class="form-control @error('date') is-invalid @enderror"
                                                    id="date" name="date"
                                                    value="{{ old('date', now()->today()->format('Y-m-d')) }}" required>
                                                @error('date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>

                                            <div class="table-responsive">
                                                <table class="table table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Student Name</th>
                                                            <th>Gender</th>
                                                            <th>Present</th>
                                                            <th>Absent</th>
                                                            <th>Late</th>
                                                            <th>Excused</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse ($students as $student)
                                                            <tr>
                                                                <td>{{ $loop->iteration }}</td>
                                                                <td>{{ $student->user->name }}</td>
                                                                <td>{{ $student->user->gender }}</td>
                                                                <td><input type="radio" name="attendance[{{ $student->id }}]" value="present" required></td>
                                                                <td><input type="radio" name="attendance[{{ $student->id }}]" value="absent"></td>
                                                                <td><input type="radio" name="attendance[{{ $student->id }}]" value="late"></td>
                                                                <td><input type="radio" name="attendance[{{ $student->id }}]" value="excused"></td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center">No students found.</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>

                                            <button type="submit" class="btn btn-primary">Submit Attendance</button>
                                        </form>
                                    </div>

                                    <!-- Attendance Records Tab -->
                                    <div class="tab-pane fade" id="attendance-records" role="tabpanel">
                                        <div class="mb-3 d-flex flex-column flex-md-row align-items-md-center justify-content-between">
                                            <h4 class="mb-2 mb-md-0">Attendance on {{ $date }}</h4>
                                        
                                            <div class="flex-wrap d-flex align-items-center">
                                                <!-- Gender Filter -->
                                                <div class="mb-2 mr-3 form-group mb-md-0">
                                                    <label for="genderFilter" class="mr-2">Filter by Gender:</label>
                                                    <select class="w-auto form-control" id="genderFilter">
                                                        <option value="">All</option>
                                                        <option value="male">Male</option>
                                                        <option value="female">Female</option>
                                                    </select>
                                                </div>
                                        
                                                <!-- Search Field -->
                                                <div class="mb-2 form-group mb-md-0">
                                                    <label for="searchStudent" class="mr-2">Search:</label>
                                                    <input type="text" class="w-auto form-control" id="searchStudent" placeholder="Enter student name">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <table class="table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Student Name</th>
                                                    <th>Gender</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if ($attendances ?? false)
                                                    @foreach ($attendances as $attendance)
                                                    {{-- {{ trim(\Str::lower($attendance->student->user->gender) )}} --}}
                                                    <tr class="attendance-row" data-gender="{{ trim(strtolower($attendance->student->user->gender)) }}">

                                                        <td>{{ $loop->iteration }}</td>
                                                        <td class="student-name">{{ $attendance->student->user->name }}</td>
                                                        <td>{{ $attendance->student->user->gender }}</td>
                                                        <td>{{ ucfirst($attendance->status) }}</td>
                                                    </tr>
                                                    
                                                    @endforeach
                                                @else
                                                    <tr>
                                                        <td colspan="4" class="text-center">No attendance found.</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div> <!-- End of Tab Content -->
                            </div>
                        </div>
                    
                </div>
            </div></div>
        </section>
    </div>
</x-layout>

<script>
    $(document).ready(function() {
        $('#stream').on('change', function() {
            $('#streamFilterForm').submit();
        });

        // Bootstrap tab activation if using raw HTML without remembering selected tab
        $('#attendanceTabs a').on('click', function(e) {
            e.preventDefault();
            $(this).tab('show');
        });

        $('#genderFilter').on('change', function() {
    let selectedGender = $(this).val().toLowerCase(); // Normalize input
    
    $('.attendance-row').each(function() {
        let rowGender = $(this).attr('data-gender').trim().toLowerCase(); // Normalize attribute
      
        
        $(this).toggle(selectedGender === "" || rowGender === selectedGender);
    });
});

        $('#searchStudent').on('keyup', function() {

            console.log('hellow')
            let searchValue = $(this).val().toLowerCase();
            $('.student-name').each(function() {
                $(this).closest('tr').toggle($(this).text().toLowerCase().includes(searchValue));
            });
        });
    });
</script>
