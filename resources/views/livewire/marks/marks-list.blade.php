<div>
    <div class="row">
        <div class="col-12">
            <div class="p-3 card">
                <div class="card-body">
                    @php
                        $examinationsTypeName = collect($examinationsTypes)->firstWhere('id', $selectedexaminationsType)?->name;
                    @endphp
                    <span style="font-size: 1.3rem; font-weight: bold;"> Detailed {{ $examinationsTypeName }} Results </span>
                    <span style="font-size: 1.1rem;" class="text-muted">| {{ auth()->user()->school->name }} | {{ $academicYear }} </span>
                    <span style="display: block; margin-bottom: 20px;"></span>

                    {{-- Filters Section --}}
                    <div class="row">
                        <div class="col-md-2">
                            <label for="selectedClass">Filter by Class:</label>
                            <select wire:model.live="selectedClass" id="selectedClass" class="form-control">
                                <option value="">Select Class</option>
                                @foreach ($classes as $class)
                                    <option value="{{ $class->id }}" @if ($class->id == $selectedClass) selected @endif>{{ $class->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if ($selectedClass && $showStreams)
                            <div class="col-md-2">
                                <label for="selectedStream">Filter by Stream:</label>
                                <select wire:model.live="selectedStream" id="selectedStream" class="form-control">
                                    <option value="">Select Stream</option>
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
                            <select wire:model.live="selectedexaminationsType" id="selectedexaminationsType" class="form-control">
                                <option value="">Select Examination Type</option>
                                @foreach ($examinationsTypes as $examinationsType)
                                    <option value="{{ $examinationsType->id }}" @if($examinationsType->id == $examTypeId) selected @endif>
                                        {{ $examinationsType->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        @if(auth()->user()->hasAnyRole(['header school','assistant headteacher','academic teacher']))
                            <div class="col-12 col-md-2">
                                <label for="selectedAcademicYear">Filter by Year:</label>
                                <select class="form-control" wire:model="selectedAcademicYear" wire:change="fetchResults">
                                    <option value="" disabled selected>-- Select Academic Year --</option>
                                    @foreach ($academicYears as $year)
                                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="col-12 col-md-2">
                            <label for="student_search">Filter by Student:</label>
                            <input type="text" wire:model.live="search" id="student_search" class="form-control" placeholder="search by surname,registration number">
                        </div>


                        {{-- <div class="col-md-2">
                            @if ($selectedClass || $selectedStream || $search || $selectedexaminationsType)
                                <a href="#" wire:click="resetFilters" class="mt-4 btn btn-primary">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            @endif
                        </div> --}}
                        {{-- <div class="col-md-2">
                            @if ($studentsMarks->isNotEmpty() && !$showNoMarksAlert)
                                <button class="btn btn-success btn-smk" wire:click="downloadResultReport">
                                    <i class="fas fa-download"></i> Download
                                </button>
                            @endif
                        </div> --}}
                    </div>

                    <hr>
                    
                    @if ($showNoMarksAlert)
                        <div class="alert alert-warning text-center" role="alert">
                            No results found for this selection.
                        </div>
                    @else
                        <div wire:init="$dispatch('open-accordion')">
                            @if($selectedexaminationsType && ($examinationsTypeName === 'Monthly' || $examinationsTypeName === 'Midterm'))
                                @foreach ($months as $monthKey => $monthName)
                                    @php
                                        $studentsForThisMonth = $studentsMarks->filter(function($student) use ($monthKey) {
                                            return $student->marks->where('month', $monthKey)->isNotEmpty();
                                        });
                                    @endphp

                                    @if ($studentsForThisMonth->isNotEmpty())
                                        <div id="accordion-{{ $monthKey }}">
                                            <div class="accordion">
                                                <div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#panel-body-inside-{{ $monthKey }}" aria-expanded="false">
                                                    <h4> {{ $monthName }}</h4>
                                                </div>
                                                <div class="accordion-body collapse px-0" id="panel-body-inside-{{ $monthKey }}" data-parent="#accordion-{{ $monthKey }}">
                                                    <div class="table-responsive">
                                                        @php
                                                            $sortedStudentsForMonth = $studentsForThisMonth->sortByDesc(function ($student) use ($monthKey) {
                                                                $mark = $student->marks->where('month', $monthKey)->first();
                                                                return $mark ? $mark->obtained_marks : -1;
                                                            });
                                                        @endphp

                                                        <table class="table table-bordered">
                                                            <thead>
                                                                <tr>
                                                                    <th rowspan="2">#</th>
                                                                    <th rowspan="2">Student Name</th>
                                                                    @foreach ($subjectsWithMarks as $subject)
                                                                        <th colspan="2" class="text-center"> <a style="color: inherit; font-weight: inherit;"  href="{{ route('subjects.show', ['subject' => $subject->id]) }}"> {{ $subject->name }} </a></th>
                                                                    @endforeach
                                                                    @role('academic teacher')
                                                                        <th rowspan="2">Action</th>
                                                                    @endrole
                                                                </tr>
                                                                <tr>
                                                                    @foreach ($subjectsWithMarks as $subject)
                                                                        <th>Marks</th>
                                                                        <th>Grade</th>
                                                                    @endforeach
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach ($sortedStudentsForMonth as $student)
                                                                    <tr>
                                                                        <td>{{ $loop->iteration }}</td>
                                                                        <td>{{ $student->user->name }}</td>
                                                                        @foreach ($subjectsWithMarks as $subject)
                                                                            @php
                                                                                $mark = $student->marks->where('month', $monthKey)->where('subject_id', $subject->id)->first();
                                                                                $fullDetails = ($mark->remark ?? 'N/A');
                                                                            @endphp
                                                                            <td>{{ $mark->obtained_marks ?? 'N/A' }}</td>
                                                                            <td title="{{ $fullDetails }}">{{ $mark->grade ?? 'N/A' }}</td>
                                                                        @endforeach
                                                                        @role('academic teacher')
                                                                            <td>
                                                                                @if ($student->marks->isNotEmpty())
                                                                                    @php
                                                                                        $academicYearId = $student->marks->first()->academic_year_id;
                                                                                        $examTypeId = $student->marks->first()->exam_type_id;
                                                                                    @endphp
                                                                                    <a href="{{ route('marks.edit.class', [
                                                                                        'studentId'                 => $student->id,
                                                                                        'academicYearId'            => $academicYearId,
                                                                                        'examTypeId'                => $examTypeId,
                                                                                        'selectedClass'             => $selectedClass,
                                                                                        'selectedSubject'           => $selectedSubject,
                                                                                        'selectedStream'            => $selectedStream,
                                                                                        'search'                    => $search,
                                                                                        'editStatus'                => 2,
                                                                                        'selectedexaminationsType'  => $selectedexaminationsType,
                                                                                        'month' => $monthKey
                                                                                    ]) }}" class="btn btn-warning">
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
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="marksTable">
                                        <thead>
                                            <tr>
                                                <th rowspan="2">#</th>
                                                <th rowspan="2">Student Name</th>
                                                @foreach ($subjectsWithMarks as $subject)
                                                    <th colspan="2" class="text-center">{{ $subject->name }}</th>
                                                @endforeach
                                                @role('academic teacher')
                                                    <th rowspan="2">Action</th>
                                                @endrole
                                            </tr>
                                            <tr>
                                                @foreach ($subjectsWithMarks as $subject)
                                                    <th>Marks</th>
                                                    <th>Grade</th>
                                                @endforeach
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($studentsMarks as $student)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $student->user->name }}</td>
                                                    @foreach ($subjectsWithMarks as $subject)
                                                        @php
                                                            $mark = $student->marks->where('subject_id', $subject->id)->first();
                                                            $fullDetails = 'Remark: ' . ($mark->remark ?? 'N/A');
                                                        @endphp
                                                        <td>{{ $mark->obtained_marks ?? 'N/A' }}</td>
                                                        <td title="{{ $fullDetails }}">{{ $mark->grade ?? 'N/A' }}</td>
                                                    @endforeach
                                                    @role('academic teacher')
                                                        <td>
                                                            @if ($student->marks->isNotEmpty())
                                                                @php
                                                                    $academicYearId = $student->marks->first()->academic_year_id;
                                                                    $examTypeId = $student->marks->first()->exam_type_id;
                                                                @endphp
                                                                <a href="{{ route('marks.edit.class', [
                                                                    'studentId'                 => $student->id,
                                                                    'academicYearId'            => $academicYearId,
                                                                    'examTypeId'                => $examTypeId,
                                                                    'selectedClass'             => $selectedClass,
                                                                    'selectedSubject'           => $selectedSubject,
                                                                    'selectedStream'            => $selectedStream,
                                                                    'search'                    => $search,
                                                                    'editStatus'                => 2,
                                                                    'selectedexaminationsType'  => $selectedexaminationsType
                                                                ]) }}" class="btn btn-warning">
                                                                    <i class="fas fa-edit"></i> Edit
                                                                </a>
                                                            @endif
                                                        </td>
                                                    @endrole
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="{{ 2 + count($subjectsWithMarks) * 2 + (auth()->user()->hasRole('academic teacher') ? 1 : 0) }}" class="text-center">No results found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                            <div class="pagination-wrapper" id="paginationWrapper">
                                {{ $studentsMarks->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
