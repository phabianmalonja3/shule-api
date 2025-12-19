<div>
    <div class="row">
        <div class="col-12">
            <div class="p-3 card">

                <div class="card-body">
                    @php
                        $examinationsTypeName = collect($examinationsTypes)->firstWhere('id', $selectedexaminationsType)?->name;
                    @endphp

                    <span style="font-size: 1.3rem; font-weight: bold;"> {{ $examinationsTypeName }} Subject Results </span>
                    <span style="font-size: 1.1rem;" class="text-muted">| {{ auth()->user()->school->name }} | {{ $academicYear }} </span>
                    <span style="display: block; margin-bottom: 20px;"></span>

                    <div class="row">

                        <div class="col-md-2">
                            <label for="selectedClass">Filter by Class:</label>
                            <select wire:model.live="selectedClass" id="selectedClass" class="form-control">
                                <option value="" disabled selected>Select Class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}"
                                        @if ($class->id == $selectedClass) selected @endif
                                    >{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($selectedClass && $showStreams)
                            <div class="col-md-2">
                                <label for="selectedStream">Filter by Stream:</label>
                                <select wire:model.live="selectedStream" id="selectedStream" class="form-control">
                                    <option value="" selected disabled>Select Stream</option>
                                    @foreach ($streams as $stream)
                                        <option value="{{ $stream->id }}">
                                            {{ \Str::replaceFirst('Form ', '', $stream->schoolClass->name ?? '') }}
                                            {{ $stream->alias }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-md-2">
                            <label for="selectedexaminationsType">Filter by Exam</label>
                            <select wire:model.live="selectedexaminationsType" id="selectedexaminationsType"
                                class="form-control">
                                <option value="" disabled selected>Select Examination Type</option>
                                @foreach ($examinationsTypes as $examinationsType)
                                    <option value="{{ $examinationsType->id }}"
                                        @if($examinationsType->id == $selectedexaminationsType) selected @endif
                                    >{{ $examinationsType->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="selectedSubject">Filter by Subject:</label>
                            <select wire:model.live="selectedSubject" id="selectedSubject" class="form-control">
                                <option value="" disabled selected>Select Subject</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}"
                                        @if ($subject->id == $selectedSubject) selected @endif
                                    >{{ $subject->name }} </option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-2">

                                <label for="student_search">Filter by Student:</label>
                                <input type="text" wire:model.live="search" id="student_search" class="form-control"
                                    placeholder="search by surname,registration number">

                        </div>

                        {{-- <div class="col-md-2">
                            @if ($selectedClass || $selectedStream || $search || $selectedexaminationsType || $selectedSubject)
                                <a href="#" wire:click="resetFilters" class="mt-4 btn btn-primary">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            @endif
                        </div> --}}

                        <div class="col-12 col-md-2 d-flex align-items-end">
                            @if ($results->isNotEmpty() && !$showNoMarksAlert)
                                <button class="btn btn-success btn-smk" wire:click="downloadResultReport">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            @endif
                        </div>
                    </div>

                    <hr>

                    @if ($showNoMarksAlert)
                        <div class="alert alert-warning text-center" role="alert">
                            No results found for this selection.
                        </div>
                    @else
                        @if($selectedexaminationsType && ($examinationsTypeName === 'Monthly' || $examinationsTypeName === 'Midterm'))
                            @foreach ($months as $key => $month)
                                @php
                                    $studentsInMonth = $studentsMarks->filter(function($student) use ($key) {
                                        return $student->marks->where('month', $key)->isNotEmpty();
                                    });
                                @endphp

                                @if ($studentsInMonth->isNotEmpty())
                                    @php

                                        $sortedStudentsForMonth = $studentsInMonth->sortBy(function ($student) use ($key) {
                                                                $mark = $student->marks->where('month', $key)->first();
                                                                return $mark ? $mark->position : -1;
                                                            });

                                    @endphp

                                    <div id="accordion{{ $key }}">
                                        <div class="accordion">
                                            <div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#panel-body-inside-{{ $key }}" aria-expanded="false">
                                                <h4> {{ $month }}</h4>
                                            </div>
                                            <div class="accordion-body collapse px-0" id="panel-body-inside-{{ $key }}" data-parent="#accordion{{ $key }}">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="marksTable">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Student Name</th>
                                                                <th>Marks</th>
                                                                <th>Grade</th>
                                                                <th>Remarks</th>
                                                                <th>Position</th>
                                                                @role('academic teacher')
                                                                    <th>Action</th>
                                                                @endrole
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($sortedStudentsForMonth as $student)

                                                                @php
                                                                    $mark = $student->marks->where('month', $key)->where('subject_id', $selectedSubject)->first();
                                                                    $previousMark = $student->marks
                                                                        ->where('month', '<', $key)
                                                                        ->where('subject_id', $selectedSubject)
                                                                        ->sortByDesc('month')
                                                                        ->first();
                                                                    $currentPosition = empty($selectedStream) ? null : $mark->position;
                                                                    $previousPosition = empty($selectedStream) ? null : optional($previousMark)->position;

                                                                    $positionChange = null;

                                                                    if (!is_null($previousPosition)) {
                                                                        $positionChange = $previousPosition - $currentPosition;
                                                                    }

                                                                    $tieCount = $tiesPerPosition[$student->stream_id][$key][$mark->position] ?? 1;
                                                                    $tieDisplay = ($tieCount > 1) ? "($tieCount ties)" : '';
                                                                @endphp
                                                                <tr>
                                                                    <td>{{ $loop->iteration }}</td>
                                                                    <td>{{ $student->user->name }}</td>
                                                                    <td>{{ $mark->obtained_marks ?? 'N/A' }}</td>
                                                                    <td>{{ $mark->grade ?? 'N/A' }}</td>
                                                                    <td>{{ $mark->remark ?? 'N/A' }}</td>
                                                                    <td>{{ $currentPosition ?? 'N/A' }}
                                                                        @if (!is_null($positionChange))
                                                                            @if ($positionChange > 0)
                                                                                <span class="text-success">
                                                                                    <i class="fas fa-arrow-up"></i>
                                                                                    {{ $positionChange }}
                                                                                </span>
                                                                            @elseif ($positionChange < 0)
                                                                                <span class="text-danger">
                                                                                    <i class="fas fa-arrow-down"></i>
                                                                                    {{ abs($positionChange) }}
                                                                                </span>
                                                                            @endif
                                                                        @endif
                                                                        <span class="text-muted">{{ $tieDisplay }} </span>
                                                                    </td>

                                                                    @role('academic teacher')
                                                                        <td>
                                                                            @if ($student->marks->isNotEmpty())
                                                                                @php
                                                                                    $examTypeId = $student->marks->first()->exam_type_id;
                                                                                @endphp
                                                                                <a href="{{ route('marks.edit', [
                                                                                                'studentId' => $student->id,
                                                                                                'marksId' => $mark->id,
                                                                                                'selectedSubject' => $selectedSubject?? null,
                                                                                                'selectedClass' => $selectedClass?? null,
                                                                                                'selectedStream' => $selectedStream?? null,
                                                                                                'search' => $search?? null,
                                                                                                'editStatus' => 1,
                                                                                                'selectedexaminationsType' => $examTypeId?? null
                                                                                            ]) }}" class="mx-1 btn btn-warning">
                                                                                    <i class="fas fa-edit"></i> Edit
                                                                                </a>
                                                                            @endif
                                                                        </td>
                                                                    @endrole
                                                                </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            {{-- @php

                                $hasResults = false;
                                $sortedStudentsForMonth = $studentsMarks->sortBy(function ($student) use ($selectedSubject, $selectedexaminationsType, &$hasResults) {
                                    $mark = $student->marks->where('subject_id', $selectedSubject)->where('exam_type_id', $selectedexaminationsType)->first();
                                    if ($mark) {
                                        $hasResults = true;
                                    }
                                    return $mark ? $mark->position : -1;
                                });

                            @endphp --}}

                            {{-- @if ($hasResults) --}}
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="marksTable">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Marks</th>
                                                <th>Grade</th>
                                                <th>Remarks</th>
                                                <th>Position</th>
                                                @role('academic teacher')
                                                    <th >Action</th>
                                                @endrole

                                            </tr>
                                        </thead>
                                        <tbody>

                                            @php

                                                $sortedStudentsForMonth = $studentsMarks->sortBy(function ($student) use($selectedSubject, $selectedexaminationsType){
                                                                        $mark = $student->marks->where('subject_id', $selectedSubject)->where('exam_type_id',$selectedexaminationsType)->first();
                                                                        return $mark ? $mark->position : -1;
                                                                    });

                                            @endphp

                                            @forelse ($sortedStudentsForMonth as $student)
                                                @php

                                                    $mark = $student->marks->where('subject_id', $selectedSubject)->where('exam_type_id',$selectedexaminationsType)->first();
                                                    $prevPosition = null;

                                                    if($examinationsTypeName === 'Annual'){
                                                        $previousMark = $student->marks
                                                            ->where('exam_type_id','<',$selectedexaminationsType)
                                                            ->where('subject_id', $selectedSubject)
                                                            ->sortByDesc('month')
                                                            ->first();

                                                        $prevPosition = optional($previousMark)->position;
                                                    }

                                                    $currentPosition = optional($mark)->position;
                                                    $positionChange = null;

                                                    if (!is_null($prevPosition) && !is_null($currentPosition)) {
                                                        $positionChange = $prevPosition - $currentPosition;
                                                    }

                                                    $tieCount = 1;
                                                    $tieDisplay = '';

                                                    if (!is_null($mark)) {
                                                        $tieCount = $tiesPerPosition[$mark->exam_type_id][$student->stream_id][$mark->month][$mark->position] ?? 1;
                                                        $tieDisplay = ($tieCount > 1) ? "($tieCount ties)" : '';
                                                    }
                                                @endphp

                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $student->user->name }}</td>
                                                    <td>{{ $mark->obtained_marks ?? 'N/A' }}</td>
                                                    <td>{{ $mark->grade ?? 'N/A' }}</td>
                                                    <td>{{ $mark->remark ?? 'N/A' }}</td>
                                                    @if($examinationsTypeName === 'Annual')
                                                        <td>{{ $currentPosition ?? 'N/A' }}
                                                            @if (!is_null($positionChange))
                                                                @if ($positionChange > 0)
                                                                    <span class="text-success">
                                                                        <i class="fas fa-arrow-up"></i>
                                                                        {{ $positionChange }}
                                                                    </span>
                                                                @elseif ($positionChange < 0)
                                                                    <span class="text-danger">
                                                                        <i class="fas fa-arrow-down"></i>
                                                                        {{ abs($positionChange) }}
                                                                    </span>
                                                                @endif
                                                            @endif
                                                            <span class="text-muted">{{ $tieDisplay }} </span>
                                                        </td>
                                                    @else
                                                        <td> @if (!is_null($mark)) {{ $mark->position }} @endif <span class="text-muted">{{ $tieDisplay }} </span> </td>
                                                    @endif

                                                    @role('academic teacher')
                                                        <td>
                                                            @if ($student->marks->isNotEmpty())
                                                                @php
                                                                    $examTypeId = $student->marks->first()->exam_type_id;
                                                                @endphp
                                                                <a href="{{ route('marks.edit', [
                                                                                'studentId' => $student->id,
                                                                                'marksId' => $mark->id,
                                                                                'selectedSubject' => $selectedSubject?? null,
                                                                                'selectedClass' => $selectedClass?? null,
                                                                                'selectedStream' => $selectedStream?? null,
                                                                                'search' => $search?? null,
                                                                                'editStatus' => 1,
                                                                                'selectedexaminationsType' => $examTypeId?? null
                                                                            ]) }}" class="mx-1 btn btn-warning">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                            @endif
                                                        </td>
                                                    @endrole
                                                </tr>
                                            @empty
                                                @php
                                                    // Update colspan calculation for the new Remarks column

                                                        $colSpan = 5 + (auth()->user()->hasRole('academic teacher') ? 1 : 0);

                                                @endphp
                                                <tr>
                                                    <td colspan="{{ $colSpan }}" class="text-center">No marks found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            {{-- @else
                                <div class="alert alert-warning text-center" role="alert">
                                    No results found for this selection.
                                </div>
                            @endif --}}
                        @endif
                        <div class="pagination-wrapper" id="paginationWrapper">
                            {{ $studentsMarks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
