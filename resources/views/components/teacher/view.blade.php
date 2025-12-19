<x-layout>
    <x-slot:title>
        Teacher Details
    </x-slot:title>

    <x-navbar />
    <x-admin.sidebar />

    <div class="main-content" style="min-height: 635px;">
        <section class="section">          
            <div class="section-body">
                <div class="row">
                    <!-- Teacher Details Section -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h3> Teacher Details </h3>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th>Profile Picture</th>
                                            <td>  <img src="{{ asset($teacher->profile_picture ? 'storage/' . $teacher->profile_picture : '/profile/png-clipart-profile-logo-computer-icons-user-user-blue-heroes-thumbnail.png') }}"
                                                alt="Profile Picture"
                                                style="width: 100px; height: 100px; border-radius: 50%; border: 2px solid #ccc;"></td>
                                        </tr>
                                        <tr>
                                            <th>Name</th>
                                            <td>{{ $teacher->name }}</td>
                                        </tr>
                                        <tr>
                                            <th>Gender</th>
                                            <td>{{ ucfirst($teacher->gender) }}</td>
                                        </tr>
                                        <tr>
                                            <th>Email</th>
                                            <td>{{ $teacher->email =='' ? 'N/A' : $teacher->email }}</td>
                                        </tr>
                                        <tr>
                                            <th>Phone Number</th>
                                            <td>{{ $teacher->phone }}</td>
                                        </tr>
                                        <tr>
                                            <th>Role</th>
                                            <td>
                                                @if($teacher->roles->first()->name == 'class teacher' || $teacher->roles->first()->name == 'Class Teacher')
                                                    Class teacher
                                                @elseif($teacher->roles->first()->name == 'header teacher' || $teacher->roles->first()->name == 'Head Teacher')
                                                    @if(Str::contains(strtolower(auth()->user()->school->name),'secondary')) Head of School @else Headteacher @endif
                                                @else
                                                    {{ $teacher->roles->first()->name }}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Subjects & Classes</th>
                                            <td>
                                                @if ($teacher->streamSubjects->isNotEmpty())
                                                    @php
                                                        $subjectsGrouped = $teacher->streamSubjects->groupBy('subject.name');
                                                    @endphp

                                                    @foreach ($subjectsGrouped as $subjectName => $assignments)
                                                        {{ $subjectName }} -
                                                        @php
                                                            $streamsByClass = $assignments->groupBy(function($assignment) {
                                                                return $assignment->stream->schoolClass->name;
                                                            });
                                                        @endphp

                                                        @foreach ($streamsByClass as $className => $classAssignments)
                                                            {{ $className }}
                                                            @if(count($classAssignments) > 0)
                                                                @php
                                                                    $validAliases = $classAssignments->pluck('stream.alias')->filter()->all();
                                                                @endphp

                                                                @if(count($validAliases) > 1)
                                                                    (@foreach ($validAliases as $index => $alias)
                                                                        {{ $alias }}@if($index == count($validAliases) - 2) & @elseif(!$loop->last),@endif
                                                                    @endforeach)
                                                                @elseif(count($validAliases) == 1)
                                                                    ({{ $validAliases[0] }})
                                                                @endif
                                                            @endif
                                                            @if(!$loop->last), @endif
                                                        @endforeach
                                                        @if(!$loop->last)<br>@endif
                                                    @endforeach
                                                @else
                                                    No Subjects/Classes Assigned
                                                @endif
                                            </td>
                                        </tr>

                                        @if ($teacher->streamSubjects->isNotEmpty())
                                            <!-- Uploaded Notes Section -->
                                            <tr>

                                            </tr>

                                            <!-- Uploaded Assignments Section -->
                                            <tr>
                                                <th>Uploaded Assignments</th>
                                                <td>
                                                    @if ($teacher->assignments->isNotEmpty())
                                                        @foreach ($teacher->assignments as $assignment)
                                                            <strong>{{ $assignment->title }}</strong><br>
                                                            Date Uploaded: {{ $assignment->created_at->format('d M, Y') }}<br>
                                                            <a href="{{ asset('uploads/assignments/' . $assignment->file) }}" target="_blank">View Assignment</a><br><br>
                                                        @endforeach
                                                    @else
                                                        No Assignments Uploaded
                                                    @endif
                                                </td>
                                            </tr>

                                            <tr>
                                                <th>Results</th>
                                                {{-- <td>
                                                    @if ($teacher->results->isNotEmpty())
                                                        @foreach ($teacher->results as $result)
                                                            Exam Type: {{ $result->exam_type }}<br>
                                                            Date Uploaded: {{ $result->created_at->format('d M, Y') }}<br>
                                                            <a href="{{ asset('results/' . $result->file) }}" target="_blank">View Results</a><br><br>
                                                        @endforeach
                                                    @else
                                                        No Results Uploaded
                                                    @endif
                                                </td> --}}
                                            </tr>

                                            <tr>
                                                <th>Result Modification History</th>
                                                <td>
                                                    {{-- {{ $teacher->audits }} --}}
                                                    {{-- @if ($teacher->resultHistory->isNotEmpty())
                                                        <ul>
                                                            @foreach ($teacher->resultHistory as $history)
                                                                <li>
                                                                    <strong>Date Modified:</strong> {{ $history->created_at->format('d M, Y') }}<br>
                                                                    <strong>Student Name:</strong> {{ $history->student_name }}<br>
                                                                    <strong>Class:</strong> {{ $history->class }}<br>
                                                                    <strong>Stream:</strong> {{ $history->stream }}<br>
                                                                    <strong>Old Marks:</strong> {{ $history->old_marks }}<br>
                                                                    <strong>New Marks:</strong> {{ $history->new_marks }}<br>
                                                                    <a href="{{ asset('result_history/' . $history->file) }}" target="_blank">Download History</a><br>
                                                                </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        No Result Modifications
                                                    @endif --}}
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- Back Button -->
                <a href="{{ route('teachers.index') }}" class="mt-4 btn btn-primary">Cancel</a>
            </div>
        </section>
    </div>
</x-layout>
