<div class="card-body">

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fa fa-triangle-exclamation"></i>
            <strong>Whoops! Something went wrong.</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit.prevent="uploadMarks">
        @if($class_status != 1)
            <div class="mb-3 form-group">
                <label for="subject_id">Subject</label>
                <select class="form-control @error('subject_id') is-invalid @enderror" id="subject_id"
                    wire:model.live="subject_id" required>
                    <option value="" disabled selected>-- Select Subject --</option>
                    @foreach ($subjectsAssigned as $subject)
                        <option value="{{ $subject->id }}" {{ $subject->id == $selectedSubject ? 'selected' : '' }}>{{ $subject->name }}</option>
                    @endforeach
                </select>
                @error('subject_id')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            @if ($classes->count() > 1)
                <div class="mb-3 form-group">
                    <label for="class_id">Class</label>
                    <select class="form-control @error('class_id') is-invalid @enderror" id="class_id"
                        wire:model.live="class_id" required>
                        <option value="" disabled selected>-- Select Class --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            @if ($showStreams)
                <div class="mb-3 form-group">
                    <label for="stream_id">Stream</label>
                    <select class="form-control @error('stream_id') is-invalid @enderror" id="stream_id"
                        wire:model.live="stream_id" required>
                        <option value="" disabled selected>-- Select Stream --</option>
                        @foreach ($streams as $stream)
                            <option value="{{ $stream->id }}">
                                @if ($showClassNameInStreamDropdown)
                                    {{ $stream->schoolClass->name }} {{ $stream->alias }}
                                @else
                                    Stream {{ $stream->alias }}
                                @endif
                            </option>
                        @endforeach
                        <option value="0">All Streams</option>
                    </select>
                    @error('stream_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        @else
            @if ($classes->count() > 1)
                <div class="mb-3 form-group">
                    <label for="class_id">Class</label>
                    <select class="form-control @error('class_id') is-invalid @enderror" id="class_id"
                        wire:model.live="class_id" required>
                        <option value="" disabled selected>-- Select Class --</option>
                        @foreach ($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                    @error('class_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @else
                <input wire:model="class_id" value="{{ $class_id }}" hidden />
            @endif

            @if ($subjectsAssigned->count() > 1)
                <div class="mb-3 form-group">
                    <label for="subject_id">Subject</label>
                    <select class="form-control @error('subject_id') is-invalid @enderror" id="subject_id"
                        wire:model.live="subject_id" required>
                        <option value="" disabled selected>-- Select Subject --</option>
                        @foreach ($subjectsAssigned as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                    @error('subject_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @else
                <input wire:model="subject_id" value="{{ $subject_id }}" hidden />
            @endif

            @if ($showStreams && $streams->isNotEmpty())
                <div class="mb-3 form-group">
                    <label for="stream_id">Stream</label>
                    <select class="form-control @error('stream_id') is-invalid @enderror" id="stream_id"
                        wire:model.live="stream_id" required>
                        <option value="" disabled selected>-- Select Stream --</option>

                        @foreach ($streams as $stream)
                            <option value="{{ $stream->id }}"> {{ $stream->schoolClass->name }} {{ $stream->alias }}
                            </option>
                        @endforeach
                        <option value="0">All Streams</option>
                    </select>
                    @error('stream_id')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            @endif
        @endif

        <div class="mb-3 form-group">
            <label for="exam_type">Examination Type</label>
            <select class="form-control @error('exam_type') is-invalid @enderror" id="exam_type"
                wire:model="exam_type" wire:change='updateExamType'>
                <option value="" selected>-- Select Examination Type --</option>
                @foreach ($exam_types as $exam_type)
                    <option value="{{ $exam_type->id }}">{{ \Str::title($exam_type->name) }}</option>
                @endforeach
            </select>
            @error('exam_type')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        @if($isMonthly || $isMidterm)
            <div class="row">
                <div class="mb-3 form-group col-md-12">
                    <label for="selectedMonth">Month</label>
                    <select class="form-control @error('selectedMonth') is-invalid @enderror" id="selectedMonth"
                        wire:model="selectedMonth" required>
                        <option value="" disabled selected>-- Select Month --</option>
                        @foreach ($months as $key => $month)
                            <option value="{{ $key }}" {{ $key > $currentMonth ? 'disabled' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                    @error('selectedMonth')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @elseif($isWeekly || $isOther)
            <div class="row">
                <div class="mb-3 form-group col-md-6">
                    <label for="selectedMonth">Month</label>
                    <select class="form-control @error('selectedMonth') is-invalid @enderror" id="selectedMonth"
                        wire:model="selectedMonth" required>
                        <option value="" disabled selected>-- Select Month --</option>
                        @foreach ($months as $key => $month)
                            <option value="{{ $key }}" {{ $key > $currentMonth ? 'disabled' : '' }}>
                                {{ $month }}
                            </option>
                        @endforeach
                    </select>
                    @error('selectedMonth')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-group col-md-6">
                    <label for="selectedWeek">Week</label>
                    <select class="form-control @error('selectedWeek') is-invalid @enderror" id="selectedWeek"
                        wire:model="selectedWeek" required>
                        <option value="" disabled selected>-- Select Week --</option>
                        @foreach ($weeks as $week)
                            <option value="{{ $week }}">{{ 'Week ' . $week }}</option>
                        @endforeach
                    </select>
                    @error('selectedWeek')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        @endif

        <div class="mb-3 form-group">
            <label for="marks_file">Upload Marks File </label>
            <input type="file" class="form-control @error('marks_file') is-invalid @enderror"
                wire:model="marks_file" accept=".xlsx,.xls">
            <span wire:loading wire:target="marks_file">
                <i class="fa fa-spinner fa-spin"></i> Loading ...
            </span>
            @error('marks_file')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
            <small class="form-text text-muted">
                Formats: .xlsx, .xls | Maximum size: 5MB
            </small>
        </div>

        <div class="mb-3 d-flex">
            <button type="submit" class="mx-2 btn btn-primary" wire:loading.attr="disabled">
                <i class="fa fa-upload"></i> Upload Student Marks
                <span wire:loading wire:target="uploadMarks">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </span>
            </button>

            <button type="button" class="btn btn-success" wire:click.prevent="downloadTemplate"
                wire:loading.attr="disabled">
                <i class="fa fa-download"></i> Download Marks Template
                <span wire:loading wire:target="downloadTemplate">
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                </span>
            </button>
        </div>
    </form>

    @if (session()->has('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(count($exam_uploads) > 0)   
        <div class="card-header">
            <h4>  {{ \Str::title('Uploaded Marks') }}</h4>
        </div>
        <div class="p-3 card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr >
                            <th>#</th> <!-- Serial Number Column -->
                            <th>Class</th>
                            <th>Stream</th>
                            <th>Subject</th>
                            <th>Exam Type</th>
                            <th>Uploaded At</th>
                            <th>Updated At</th>
                            <th class="ml-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($exam_uploads as $index => $upload)
                            <tr>
                                <td >{{ $index + 1 }}</td>
                                <td> {{ $upload->schoolClass->name }} </td>
                                <td>  
                                    @php
                                        $streams = $examDataMap[$upload->id]['streams'] ?? [];
                                        $streamCount = count($streams);
                                    @endphp

                                    @foreach($streams as $index => $stream)
                                        {{ $stream }} @if($streamCount > 2 && $index < $streamCount - 1), @endif @if($streamCount > 1 && $index == $streamCount - 2) & @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if($examDataMap[$upload->id]['subjects']) {{ array_values($examDataMap[$upload->id]['subjects'])[0] }} @endif
                                </td>
                                <td> {{ $upload->examinationType->name }}
                                    @foreach($months as $key => $month) 
                                        @if($key == $upload->month && $upload->examinationType->name == 'Monthly') ({{ $month }}) @endif
                                    @endforeach
                                </td>                                                                                     
                                <td> {{ $upload->created_at }} </td>
                                <td> {{ $upload->updated_at }} </td>      
                                <td>
                                    <div class="flex-row gap-3 d-flex justify-content-left align-items-left">
                                        <a href="{{ route('marks.index', [
                                                    'selectedClass' => $upload->school_class_id,
                                                    'selectedSubject' => $examDataMap[$upload->id]['subjects']? array_keys($examDataMap[$upload->id]['subjects'])[0] : null,
                                                    'selectedexaminationsType' => $upload->examinationType->id,
                                                    'subjectStatus'=>1
                                                ]) }}" 
                                        class="mb-2 btn btn-info btn-sm d-flex align-items-center"
                                            style="  margin-right: 4px; margin-top:7px;">
                                            <i class="fas fa-info-circle me-2"></i> View
                                        </a>

                                        <a href="{{ route('marks.upload.reset', ['exam_upload_id' => $upload->id]) }}"
                                            class="mb-2 btn btn-warning btn-sm d-flex align-items-center "
                                            
                                            style="  margin-right: 4px; margin-top:7px;" >
                                            <i class="fas fa-edit me-2"></i> Reset
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
