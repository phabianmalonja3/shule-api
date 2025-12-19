<x-layout>
    <x-slot:title>
        Parent Dashboard
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar :school="$school" />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">
            <div class="section-body">
                <div class="container">
                    <div class="row">
                        <!-- First Column: Profile -->
                        <div class="col-md-4">
                            <div class="text-center profile-section">
                                <img src="{{ asset('profile/png-clipart-profile-logo-computer-icons-user-user-blue-heroes-thumbnail.png') }}"
                                    alt="Profile Picture" class="profile-pic-rect">

                                <h5 class="mt-3">Name :{{ $students->first()->user->name }}</h5>
                                <h5 class="mt-3">Class :{{ $students->first()->stream->schoolClass->name }}
                                    {{ $students->first()->stream->alias }}</h5>
                            </div>
                        </div>

             
                        <div class="col-md-8">
                            <div class="row">
                                @php

                                    $links = [
                                        [
                                            'url' => route('student.examination.results', [
                                                'student' => $students->first()->id,
                                            ]),
                                            'text' => 'Results',
                                            'icon' => 'fas fa-poll',
                                        ],
                                        [
                                            'url' => route('student.attandancy.show', [
                                                'student' => $students->first()->id,
                                            ]),
                                            'text' => 'Attendance',
                                            'icon' => 'fas fa-user-check',
                                        ],
                                        // ['url' => route('student.assignment',['student'=>$students->first()->id]), 'text' => 'Assignment', 'icon' => 'fas fa-tasks'],
                                        ['url' => '#', 'text' => 'School Calendar', 'icon' => 'fas fa-calendar'],
                                        [
                                            'url' => '#',
                                            'text' => 'Timetable',
                                            'icon' => 'fas fa-calendar-alt',
                                        ],
                                        [
                                            'url' => route('student.subject.teacher', [
                                                'student' => $students->first()->id,
                                            ]),
                                            'text' => 'Teachers',
                                            'icon' => 'fas fa-chalkboard-teacher',
                                        ],
                                        ['url' => '', 'text' => 'Annoucements', 'icon' => 'fas fa-bell'],
                                        [
                                            'url' => route('parent.payment'),
                                            'text' => 'Payments',
                                            'icon' => 'fas fa-money-check-alt',
                                        ],
                                    ];
                                @endphp



                                @foreach ($links as $index => $link)
                                    <div class="py-1 mb-3 col-md-3 ">
                                        @if ($link['text'] == 'Timetable')
                                            <a href="#" id="timetbl-btn" class="card-link" data-toggle="modal"
                                                data-target=".bd-example-modal-lg">
                                                <div class="info-card">
                                                    <i class="{{ $link['icon'] }} icon"></i>
                                                    <h5>{{ $link['text'] }}</h5>

                                                </div>

                                            </a>

                                            @elseif ($link['text'] == 'Teachers')
                                            <a href="#" id="timetbl-btn" class="card-link" data-toggle="modal"
                                            data-target=".teachers-model">
                                            <div class="info-card">
                                                <i class="{{ $link['icon'] }} icon"></i>
                                                <h5>{{ $link['text'] }}</h5>

                                            </div>

                                        </a>
                                        @else
                                            <a href="{{ $link['url'] }}" class="card-link">
                                                <div class="info-card">
                                                    <i class="{{ $link['icon'] }} icon"></i>
                                                    <h5>{{ $link['text'] }}</h5>
                                                </div>
                                            </a>
                                        @endif

                                    </div>
                                    
                                @endforeach
                            </div>
                        </div>
                    </div>


                </div>
            </div>
        </section>
    </div>

</x-layout>
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Class TimeTable </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-primary">
                            <tr>

                                <th>#</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Subject</th>
                                <th>Teacher</th>
                                <th>Stream</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade teachers-model" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="myLargeModalLabel">Student's Teachers </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- <div class="card-body">
                    <div class="p-0 card-body"> --}}
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr class="text-left">
                                        <th>#</th> <!-- Serial Number -->
                                        <th>Full Name</th>
                                        <th>Phone Number</th>
                                        <th>Subject Name</th>
                                        <th>Action</th>
                
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($teachers as $index => $teacher)
                                        <tr>
                                            <!-- Serial Number -->
                                            <td>{{ $index + 1 }}</td>
                
                                            <!-- Teacher Details -->
                                            <td>{{ $teacher->teacher->name }}</td>
                                            <td>{{ $teacher->teacher->phone }}</td>
                                            <td>{{ $teacher->subject->name }}</td>
                                            
                
                                           
                                         
                                           
                
                                            <!-- Actions -->
                                            <td class="">
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('teachers.show', $teacher->id) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-info-circle "></i> Details
                                                    </a>   
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No teachers found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
        
                            <div x-intersect.full="$wire.loadMore()" class="p-2 text-center">
                                <div wire:loading wire:target="loadMore" class="text-center">
                                    <div class="lds">
                                        <div></div><div></div><div></div><div></div>
                                    </div>
                                </div>
                            </div>
                        {{-- </div>
                    </div> --}}
                </div>
            </div>
        </div>
    </div>
</div>


<style>


.profile-pic-rect {
            width: 100%;
            max-width: 250px;
            /* Adjust as needed */
            height: 260px;
            /* Adjust height */
            object-fit: cover;
            border-radius: 10px;
            /* Soft rounded corners */
            border: 10px solid white;
        }

        /* Card styling */
        .info-card {
            width: 100%;
            height: 120px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease-in-out;
            text-align: center;
            font-weight: bold;
            color: #333;
        }

        .info-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
            background: #007bff;
            color: white;
        }

        .info-card .icon {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .card-link {
            text-decoration: none;
            display: block;
        }
    .table-primary {
        background-color: #cce5ff;
        /* Light blue header */
    }

    .table-info {
        background-color: #e7f3fe;
        /* Light blue for odd rows */
    }

    .table-secondary {
        background-color: #f8f9fa;
        /* Light grey for even rows */
    }

    .table th {
        text-align: center;
        /* Center align the headers */
    }

    .table td {
        vertical-align: middle;
        /* Center align the content */
    }
</style>

<div class="lds">
    <div></div><div></div><div></div><div></div>
</div>
<script>
  $(document).ready(function () {
    $('#timetbl-btn').on('click', function () {
        const spinner = $('#loader'); // Add a loader if needed
        const timetableBody = $('.modal-body tbody'); // Select the table body

        spinner.show(); // Show loading spinner
        timetableBody.html('<tr><td colspan="6" class="text-center"> <div class="lds"><div></div><div></div><div></div><div></div></div></td></tr>'); // Placeholder text

        const url = "{{ route('timetable.student.show', ['classId' => $students->first()->schoolClass->id]) }}";

        console.log(url);
        fetch(url)
            .then(response => response.json())
            .then(data => {
                spinner.hide(); // Hide loading spinner
                timetableBody.empty(); // Clear old data

                if (!data || typeof data !== 'object') {
                    timetableBody.html('<tr><td colspan="6" class="text-center">Invalid timetable data.</td></tr>');
                    return;
                }

                console.log(data);

                // Loop through each day's timetable
                Object.entries(data).forEach(([day, schedules]) => {
                    // Make sure the schedules is an array
                    if (Array.isArray(schedules) && schedules.length > 0) {
                        timetableBody.append(`<tr class="table-info"><td colspan="6" class="text-center"><strong>${day}</strong></td></tr>`);

                        schedules.forEach((schedule, index) => {
                            timetableBody.append(`
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${schedule.start_time}</td>
                                    <td>${schedule.end_time}</td>
                                    <td>${schedule.subject}</td>
                                    <td>${schedule.teacher}</td>
                                    <td>${schedule.stream}</td>
                                </tr>
                            `);
                        });
                    } else {
                        timetableBody.append(`<tr><td colspan="6" class="text-center">No timetable for ${day}.</td></tr>`);
                    }
                });
            })
            .catch(error => {
                console.error("Error fetching timetable:", error);
                timetableBody.html('<tr><td colspan="6" class="text-center text-danger">Failed to load timetable.</td></tr>');
            });
    });
});



</script>

