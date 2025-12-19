<div>

    <span style="font-size: 1.3rem; font-weight: bold;"> Summary of {{ $examType }} Results </span>
    <span style="font-size: 1.1rem;" class="text-muted">| {{ auth()->user()->school->name }} | {{ $academic_year }} </span>
    <span style="display: block; margin-bottom: 30px;"></span>
    <div class="row">
        <!-- Class Filter -->

        {{-- @if (auth()->user()->hasAnyRole(['academic teacher', 'header teacher', 'assistant headteacher'])) --}}
            <div class="col-12 col-md-2">
                <label for="selectedClass">Filter by Class:</label>
                <select class="form-control" wire:model="selectedClass" wire:change="fetchResults">
                    <option value="" disabled selected>-- Select Class --</option>
                    @foreach ($classes as $class)
                        <option value="{{ $class->id }}" {{ $selectedClass == $class->id? 'selected' : '' }}>{{ $class->name }} </option>
                    @endforeach
                </select>
            </div>
        {{-- @else --}}

        @if(count($streams) > 1)
        <div class="col-12 col-md-2">
            <label for="selectedStream">Filter by Stream: </label>
            <select class="form-control" wire:model="selectedStream" wire:change="fetchResults">
                <option value="" selected @if(auth()->user()->hasRole('class teacher')) disabled @endif>-- Select Stream --</option>
                @foreach ($streams as $stream)
                    <option value="{{ $stream->id }}" {{ $selectedStream == $stream->id? 'selected' : '' }}>
                        {{ \Str::replaceFirst('Form ', '', $stream->schoolClass->name ?? '') }}
                        {{ $stream->alias }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif
        {{-- @endif --}}

        <!-- Exam Type Filter -->
        <div class="col-12 col-md-2">
            <label for="selectedExaminationType">Filter by Exam:</label>
            <select class="form-control" wire:model="selectedExaminationType" wire:change="fetchResults">
                <option value="" disabled selected>-- Select Exam Type --</option>
                @foreach ($exam_types as $exam)
                    <option value="{{ $exam->id }}" {{ $selectedExaminationType == $exam->id? 'selected' : '' }}>{{ \Str::title($exam->name) }} </option>
                @endforeach
            </select>
        </div>

        @if(auth()->user()->hasAnyRole(['header school','assistant headteacher','academic teacher']))
            <!-- Academic Year Filter -->
            <div class="col-12 col-md-2">
                <label for="selectedAcademicYear">Filter by Year:</label>
                <select class="form-control" wire:model="selectedAcademicYear" wire:change="fetchResults">
                    <option value="" disabled selected>-- Select Academic Year --</option>
                    @foreach ($academic_years as $year)
                        <option value="{{ $year->id }}">{{ $year->year }}</option>
                    @endforeach
                </select>
            </div>
        @endif

        <!-- Search Filter -->
        {{-- <div class="col-12 col-md-2">
            <label for="search">Filter by Student:</label>
            <input class="form-control" wire:model.live="search" placeholder="Enter student name or registration number"
                wire:keyup='fetchResults'>
        </div> --}}

        <div class="col-12 col-md-2">
            <label for="student_search">Filter by Student:</label>
            <input type="text" wire:model.live="search" id="student_search" class="form-control" placeholder="search by surname,registration number" wire:keyup='fetchResults'>
        </div>

        <div class="col-12 col-md-2 d-flex align-items-end">
            @if ($results->isNotEmpty() && !$showNoMarksAlert)
                <button class="btn btn-success btn-smk" wire:click="downloadReport">
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
        @if ($examType == 'Weekly' || $examType == 'other')

            <div class="col-12 col-md-6 col-lg-12">
                {{-- <div class="card"> --}}
                <div class="card-header">
                    <h4>Examination Results</h4>
                </div>
                <div class="card-body">
                    <div id="accordion">

                        @foreach ($months as $key => $month)
                            <div class="accordion">
                                <div class="accordion-header collapsed" role="button" data-toggle="collapse"
                                    data-target="#panel-body-{{ $key }}" aria-expanded="false">
                                    <h4> {{ $month }}</h4>
                                </div>
                                <div class="accordion-body collapse" id="panel-body-{{ $key }}"
                                    data-parent="#accordion" style="">

                                    <div class="col-12 col-md-6 col-lg-12">

                                        <div class="card-body">

                                            @php

                                                $weeks = [1, 2, 3, 4];
                                            @endphp
                                            @foreach ($weeks as $index => $week)
                                                <div id="accordion{{ $key }}">
                                                    <div class="accordion">
                                                        <div class="accordion-header collapsed" role="button"
                                                            data-toggle="collapse"
                                                            data-target="#panel-body-inside-{{ $index }}"
                                                            aria-expanded="false">
                                                            <h4>Week {{ $week }}</h4>
                                                        </div>
                                                        <div class="accordion-body collapse"
                                                            id="panel-body-inside-{{ $index }}"
                                                            data-parent="#accordion1" style="">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>#</th>
                                                                            <th>Student Name</th>
                                                                            <th>Registration Number</th>
                                                                            <th>Exam Type</th>
                                                                            <th>Total Marks</th>
                                                                            <th>Average Marks</th>
                                                                            {{-- <th>Position</th> --}}


                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                        @php
                                                                            $i = 1;
                                                                        @endphp
                                                                        @foreach ($results as $result)
                                                                            @if ($result->month == $key && $result->week == $index + 1)
                                                                                <tr>

                                                                                    <td>{{ $i++ }}</td>
                                                                                    <td>{{ $result->student->user->name }}
                                                                                    </td>
                                                                                    <td>{{ $result->student->reg_number }}
                                                                                    </td>
                                                                                    <td>{{ $examType }}</td>
                                                                                    <td>{{ $result->total_marks }}</td>
                                                                                    <td>{{ number_format($result->average_marks, 2) }}
                                                                                    </td>
                                                                                    {{-- <td>{{ $result->position ?? 'N/A' }} --}}
                                                                                    </td>


                                                                                </tr>
                                                                            @endif
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
            </div>
        @elseif($examType == 'Monthly' || $examType == 'Midterm')
            @foreach ($months as $key => $month)
                @php
                    $studentsInMonth = $results->filter(function($result) use ($key) {
                        return $result->month == $key;
                    });
                @endphp

                @if ($studentsInMonth->isNotEmpty())
                    @php

                        $sortedStudentsForMonth = empty($selectedStream)? $studentsInMonth->sortBy(function ($result) {
                            return $result->position;}) : $studentsInMonth->sortBy(function ($result) {
                            return $result->stream_position;});
                    @endphp

                    <div id="accordion{{ $key }}">
                        <div class="accordion">
                            <div class="accordion-header collapsed" role="button" data-toggle="collapse" data-target="#panel-body-inside-{{ $key }}" aria-expanded="false">
                                <h4>{{ $month }}</h4>
                            </div>
                            <div class="accordion-body collapse px-0" id="panel-body-inside-{{ $key }}" data-parent="#accordion{{ $key }}">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Student Name</th>
                                                <th>Total Marks</th>
                                                <th>Average Marks</th>
                                                <th>Grade</th>
                                                <th>Remark</th>
                                                <th>Position</th>
                                                <th>Report</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($sortedStudentsForMonth as $result)
                                                @php
                                                    $previousResult = $results->where('student_id', $result->student_id)
                                                                            ->where('month', '<', $key)
                                                                            ->sortByDesc('month')
                                                                            ->first();

                                                    $currentPosition = empty($selectedStream) ? $result->position : $result->stream_position;
                                                    $previousPosition = empty($selectedStream) ? optional($previousResult)->position : optional($previousResult)->stream_position;

                                                    $positionChange = null;

                                                    if (!is_null($previousPosition) && !is_null($currentPosition)) {
                                                        $positionChange = $previousPosition - $currentPosition;
                                                    }

                                                    if (empty($selectedStream)) {
                                                        $tieCount = $tiesPerPositionByClass[$result->student->school_class_id][$key][$result->position] ?? 1;
                                                    } else {
                                                        $tieCount = $tiesPerPositionByStream[$result->student->stream_id][$key][$result->stream_position] ?? 1;
                                                    }

                                                    $tieDisplay = ($tieCount > 1) ? "($tieCount ties)" : '';

                                                @endphp
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $result->student->user->name }}</td>
                                                    <td>{{ $result->total_marks }}</td>
                                                    <td>{{ number_format($result->average_marks, 2) }}</td>
                                                    <td>{{ $result->grade }}</td>
                                                    <td>{{ $result->remark }}</td>
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
                                                        <span class="text-muted"> {{ $tieDisplay }} </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('report.export', ['student' => $result->student->id]) }}" class="btn btn-primary btn-sm" target="_blank">Download</a>
                                                    </td>
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
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Student Name</th>
                            <th>Total Marks</th>
                            <th>Average Marks</th>
                            <th>Grade</th>
                            <th>Remark</th>
                            <th>Position</th>
                            {{-- @if ($examType == 'Annual' || $examType == 'Midterm') --}}
                                <th>Report</th>
                            {{-- @endif --}}
                        </tr>
                    </thead>
                    <tbody>
                        @php

                            $sortedStudentsForMonth = empty($selectedStream)? $results->where('exam_type_id', $selectedExaminationType)->sortBy(function ($result) {
                                return $result->position;}) : $results->where('exam_type_id', $selectedExaminationType)->sortBy(function ($result) {
                                return $result->stream_position;});
                        @endphp
                        @if($sortedStudentsForMonth->isNotEmpty())
                            @foreach ($sortedStudentsForMonth as $index => $result)
                                @php

                                    if($examType === 'Annual'){

                                        $previousMark = $results
                                            ->where('student_id', $result->student_id)
                                            ->where('exam_type_id','<',$result->exam_type_id)
                                            ->sortByDesc('month')
                                            ->first();
                                    }else{
                                        $previousMark = null;
                                    }

                                    $prevClassPosition = optional($previousMark)->position?? null;
                                    $prevStreamPosition = optional($previousMark)->stream_position?? null;

                                    $currentPosition = empty($selectedStream) ? $result->position : $result->stream_position;
                                    $previousPosition = empty($selectedStream) ? $prevClassPosition : $prevStreamPosition;

                                    $positionChange = null;

                                    if (!is_null($previousPosition)) {
                                        $positionChange = $previousPosition - $currentPosition;
                                    }

                                    if (empty($selectedStream)) {
                                        $tieCount = $tiesPerPositionByClass[$result->student->school_class_id][$result->month][$result->position] ?? 1;
                                    } else {
                                        $tieCount = $tiesPerPositionByStream[$result->student->stream_id][$result->month][$result->stream_position] ?? 1;
                                    }

                                    $tieDisplay = ($tieCount > 1) ? "($tieCount ties)" : '';
                                @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $result->student->user->name }}</td>
                                    <td>{{ $result->total_marks }}</td>
                                    <td>{{ number_format($result->average_marks, 2) }}</td>
                                    <td>{{ $result->grade }}</td>
                                    <td>{{ $result->remark }}</td>
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
                                        <span class="text-muted"> {{ $tieDisplay }} </span>
                                    </td>

                                    {{-- @if ($examType == 'Annual' || $examType == 'Midterm') --}}
                                        <td><a href="{{ route('report.export', ['student' => $result->student->id]) }}"
                                                class="btn btn-primary btn-sm" target="_blank">Download</a></td>
                                    {{-- @endif --}}
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        @endif
    @endif






</div>
