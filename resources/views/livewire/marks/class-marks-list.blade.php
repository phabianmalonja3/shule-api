<div>
    <div class="row">
        <div class="col-12">
            <div class="p-3 card">
                <h4 class="mx-2">Student Marks</h4>

                <div class="text-left col-md-12 ">
                    <a href="{{ url()->previous() }}" class="btn btn-primary">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">

                        @if (auth()->user()->hasAnyRole(['academic teacher', 'header teacher']))
                            <div class="col-md-2">
                                <label for="selectedClass">Filter by Class:</label>
                                <select wire:model.live="selectedClass" id="selectedClass" class="form-control">
                                    <option value="">Select Class</option>
                                    @foreach ($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>


                            @if ($selectedClass)
                                <div class="col-md-2">
                                    <label for="selectedStream">Filter by Stream:</label>
                                    <select wire:model.live="selectedStream" id="selectedStream" class="form-control">
                                        <option value="">Select Stream</option>
                                        @foreach ($streams as $stream)
                                            <option value="{{ $stream->id }}">Class
                                                {{ \Str::replaceFirst('Form ' ?? 'Class ', '', $stream->schoolClass->name) }}
                                                {{ $stream->alias }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @else
                            <div class="col-md-2">
                                <label for="selectedStream">Filter by Stream:</label>
                                <select wire:model.live="selectedStream" id="selectedStream" class="form-control">
                                    <option value="">Select Stream</option>
                                    @foreach (auth()->user()->streams as $stream)
                                        <option value="{{ $stream->id }}">
                                            {{ \Str::replaceFirst('Form ' ?? 'Class ', '', $stream->schoolClass->name) }}
                                            {{ $stream->alias }} </option>
                                    @endforeach
                                </select>
                            </div>

                        @endif
                        {{-- {{ auth()->user()->streams[0] }} --}}

                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="student_search">Filter by Student:</label>
                                <input type="text" wire:model.live="search" id="student_search" class="form-control"
                                    placeholder="search by surname,registration number">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="selectedSubject">Filter by Subject:</label>
                            <select wire:model.live="selectedSubject" id="selectedSubject" class="form-control">
                                <option value="">Default ({{ $subjects->first()->name }})</option>
                                @foreach ($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="selectedexaminationsType">Filter by Examination</label>
                            <select wire:model.live="selectedexaminationsType" id="selectedexaminationsType"
                                class="form-control">
                                <option value="">Select Examination Type</option>
                                @foreach ($examinationsTypes as $examinationsType)
                                    <option value="{{ $examinationsType->id }}">{{ $examinationsType->name }}</option>
                                @endforeach
                            </select>
                        </div>


                        <div class="col-md-2">
                            <label for="selectedGrade">Filter by Grade:</label>
                            ' <select wire:model.live="selectedGrade" id="selectedGrade" class="form-control">
                                <option value="">Select Grade</option>
                                @foreach ($grades as $grade)
                                    <option value="{{ $grade }}">{{ $grade }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if (auth()->user()->streamSubjects)
                            <div class="mt-4 col-md-2">
                                <a href="" id="" class="mt-2 btn btn-primary">
                                    <i class="fas fa-sync-alt"></i> Reset
                                </a>
                            </div>
                        @endif
                    </div>

                    <!-- Back Button -->

                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="marksTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                                <th>Examination Type</th>
                                <th>Marks</th>
                                <th>Grade</th>
                                <th>Remark</th>
                                @role('academic teacher')
                                    <th>Action</th>
                                @endrole


                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($studentsMarks as $student)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $student->reg_number }}</td>
                                    <td>{{ $student->user->name }}</td>
                                    <td> {{ $marks->examType->name ?? 'N/A' }}</td>


                                    @php

                                        if ($selectedSubject) {
                                            $marks = $student->marks->where('subject_id', $selectedSubject)->first();
                                        } else {
                                            $marks = $student->marks->first();
                                        }

                                    @endphp

                                    <td> {{ $marks->obtained_marks ?? 'N/A' }}</td>
                                    <td> {{ $marks->grade ?? 'N/A' }}</td>
                                    <td> {{ $marks->remark ?? 'N/A' }}</td>


                                    <td>
                                        <!-- Reset Button -->


                                        <!-- Edit Button -->

                                        @role('academic teacher')
                                            <a href="{{ route('marks.edit', ['studentId' => $student->id ?? '', 'marksId' => $marks->id ?? '']) }}"
                                                class="mx-1 btn btn-warning">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                        @endrole
                                    </td>
                                    {{-- {{ $marks->id }} --}}


                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center">No Marks match.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="pagination-wrapper" id="paginationWrapper">
                    {{ $studentsMarks->links() }}
                </div>
            </div>


        </div>



    </div>
