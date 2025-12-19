<x-layout>
    <x-slot:title>
        Subject {{ $subject->name }} Page
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
                                <h4>Subject Details</h4>
                                <a href="{{url()->previous()}}" class="btn btn-primary">
                                    <i class="fas fa-arrow-left"></i> Go Back
                                </a>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h5>Subject Information</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <strong>Subject Name:</strong> {{ $subject->name }}
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Number of Streams Taught :</strong> {{ $subject->streams->count()  }}
                                            </li>
                                          
                                        </ul>
                                    </div>

                                    <div class="col-md-6">
                                        <h5>Subject Performance</h5>
                                        <ul class="list-group">
                                            <li class="list-group-item">
                                                <strong>Position in School :</strong> {{ $schoolAverage ?? 'N/A' }} 
                                                <span class=" float-end">Rank {{ $schoolRank ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item">
                                                <strong> Position in Ward :</strong> {{ $wardAverage ?? 'N/A' }} 
                                                <span class="">Rank {{ $wardRank ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Position in District :</strong> {{ $districtAverage ?? 'N/A' }} 
                                                <span class="">Rank {{ $districtRank ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Position in Region :</strong> {{ $regionalAverage ?? 'N/A' }} 
                                                <span class="">Rank {{ $regionalRank ?? 'N/A' }}</span>
                                            </li>
                                            <li class="list-group-item">
                                                <strong>Position in Country :</strong> {{ $nationalAverage ?? 'N/A' }} 
                                                <span class="">Rank {{ $nationalRank ?? 'N/A' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                    
                                </div>

                                <div class="mt-4 row">
                                    <div class="col-12">
                                        <h5>Teachers Teaching this Subject in {{ date('Y') }}</h5>

                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Teacher Name</th>
                                                    <th>Classes & Streams</th>
                                                    <th>Max Score</th>
                                                    <th>Min Score</th>
                                                    <th>Average</th>
                                                    <th>Remark</th>
                                                </tr>
                                            </thead>
                                            @php
                                                $displayedTeacherIds = []; // To track displayed teacher IDs to avoid duplicates
                                                $serialNumber = 1; // Initialize serial number
                                            @endphp
                                            <tbody>
                                                @forelse ($subject->teachers as $teacher)
                                                    @if (!in_array($teacher->id, $displayedTeacherIds))
                                                        @php
                                                            $displayedTeacherIds[] = $teacher->id; // Add teacher ID to the list
                                
                                                            // Group streams by class using the stream_subjects table
                                                            $streamsByClass = \DB::table('stream_subject_teacher')
                                                                ->where('stream_subject_teacher.subject_id', $subject->id)
                                                                ->where('stream_subject_teacher.teacher_id', $teacher->id)
                                                                ->join('streams', 'stream_subject_teacher.stream_id', '=', 'streams.id')
                                                                ->join('school_classes', 'streams.school_class_id', '=', 'school_classes.id')
                                                                ->select('school_classes.name as class_name', 'streams.name as stream_name')
                                                                ->get()
                                                                ->groupBy('class_name')
                                                                ->map(function ($groupedStreams) {
                                                                    return $groupedStreams->pluck('stream_name');
                                                                });
                                
                                                            // Format classes and streams for display
                                                            $classesAndStreams = $streamsByClass->map(function ($streams, $class) {
                                                                return $class . ' (' . $streams->join(', ') . ')';
                                                            })->join(', ');
                                
                                                            // Since you don't have the scores table, leave performance columns blank
                                                            $maxScore = 'N/A';
                                                            $minScore = 'N/A';
                                                            $averageScore = 'N/A';
                                                            $remark = 'N/A';
                                
                                                            // Format teacher's name
                                                            $nameParts = explode(' ', $teacher->name);
                                                            $firstName = $nameParts[0] ?? '';
                                                            $lastName = end($nameParts);
                                                            $formattedName = ucwords(strtolower($lastName));
                                                            if (!empty($firstName)) {
                                                                $formattedName .= ' ' . strtoupper(substr($firstName, 0, 1)) . '.';
                                                            }
                                                        @endphp
                                
                                                        <tr>
                                                            <td>{{ $serialNumber++ }}</td>
                                                            <td>
                                                                <strong>{{ $formattedName }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $teacher->phone }}</small>
                                                            </td>
                                                            <td>{{ $classesAndStreams }}</td>
                                                            <td>{{ $maxScore }}</td>
                                                            <td>{{ $minScore }}</td>
                                                            <td>{{ $averageScore }}</td>
                                                            <td>{{ $remark }}</td>
                                                        </tr>
                                                    @endif
                                                @empty
                                                    <tr>
                                                        <td colspan="7" class="text-center">No teachers assigned to this subject.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                
                    </div>
                </div>
            </div>
        </section>
    </div>
</x-layout>
