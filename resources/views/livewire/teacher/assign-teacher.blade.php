
<div>
    @if (auth()->user()->hasRole('academic teacher'))
        @if(count($streams) > 0)
            <div class="card" id="assignmentForm">
                <div class="card-header">
                    <h4>{{ $isEdit ? 'Edit Subject Teacher' : 'Assign Subject Teacher' }}</h4>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <div class="card-body">
                    <form wire:submit.prevent="{{ $isEdit ? 'update' : 'assign' }}">
                        <input type="hidden" name="school_class_id" value="{{ $school_class_id }}">
                        @php $assigned_subject_ids = $assigned_teachers->pluck('subject_id')->unique(); @endphp
                        <div class="form-group">
                            <label for="subject_id">Subject</label>
                            <select wire:model.live="subject_id" class="form-control">
                                <option value="" @if ($isEdit) disabled @endif>Select Subject</option>
                                @foreach ($subjects as $subject)

                                    @php
                                        $assigned_streams_for_this_subject = $assigned_teachers->where('subject_id', $subject->id)->pluck('stream_id')->unique();
                                        $all_class_streams_count = $class->streams->count();
                                        $is_subject_fully_assigned_to_class = ($all_class_streams_count > 0 && $assigned_streams_for_this_subject->count() === $all_class_streams_count);
                                    @endphp

                                    <option value="{{ $subject->id }}"
                                        @if (!$isEdit && $is_subject_fully_assigned_to_class || $isEdit ) disabled @endif
                                        >
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="teacher_id">Teacher</label>
                            <select wire:model="teacher_id" class="form-control">
                                <option value="">Select Teacher</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        @php
                            $assigned_streams_for_selected_subject = collect(); 
                            if ($subject_id) {
                                $assigned_streams_for_selected_subject = $assigned_teachers
                                    ->where('subject_id', $subject_id)
                                    ->pluck('stream_id')
                                    ->unique();
                            }

                            $remaining_streams = $streams->filter(function($stream) use ($assigned_streams_for_selected_subject) {
                                return !$assigned_streams_for_selected_subject->contains($stream->id);
                            });

                            $can_assign_to_all_streams = $remaining_streams->count() === $class->streams->count();
                        @endphp

                        @if ($isEdit)
                            @if (empty($this->class->teacher_class_id))
                                @php
                                    $current_assignment_stream_ids = $assigned_teachers
                                        ->where('teacher_id', $teacher_id)
                                        ->where('subject_id', $subject_id)
                                        ->pluck('stream_id')
                                        ->unique()
                                        ->toArray();

                                    $all_streams_assigned_for_current_subject = $assigned_teachers
                                        ->where('subject_id', $subject_id)
                                        ->pluck('stream_id')
                                        ->unique()
                                        ->toArray();

                                    $streams_to_display_in_edit = $streams->filter(function ($stream) use ($current_assignment_stream_ids, $all_streams_assigned_for_current_subject) {
                                        $is_current_assignment = in_array($stream->id, $current_assignment_stream_ids);
                                        $is_unassigned_for_subject = !in_array($stream->id, $all_streams_assigned_for_current_subject);

                                        return $is_current_assignment || $is_unassigned_for_subject;
                                    });
                                @endphp

                                <div class="form-group">
                                    <label for="stream_id">Select Stream(s) to Assign</label>
                                    @foreach ($streams_to_display_in_edit as $stream)
                                        @php
                                            $is_current_assignment = in_array($stream->id, $current_assignment_stream_ids);
                                            $is_unassigned_for_subject = !in_array($stream->id, $all_streams_assigned_for_current_subject);
                                        @endphp
                                        <div class="form-check" >
                                            <input type="checkbox" wire:model="streams_selected" value="{{ $stream->id }}"
                                                class="form-check-input" id="stream_{{ $stream->id }}">
                                            <label class="form-check-label" for="stream_{{ $stream->id }}">
                                                {{ $stream->schoolClass->name }} {{ $stream->alias }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @else

                            @if ($subject_id && $remaining_streams->isEmpty())
                                <div class="alert alert-info">
                                    This subject is already assigned to all streams in this class.
                                </div>
                            @elseif ($subject_id && $remaining_streams->count() > 0 && empty($class->teacher_class_id))
                                @if ($can_assign_to_all_streams)
                                    <div class="form-group">
                                        <label for="all_streams">Apply to all streams?</label>
                                        <select wire:model.live="all_streams" class="form-control">
                                            <option value="null">Select Option</option>
                                            <option value="yes">Yes, apply to all streams</option>
                                            <option value="no">No, select specific streams</option>
                                        </select>
                                    </div>
                                @endif

                                @if ($all_streams === 'no' || !$can_assign_to_all_streams)
                                    <div class="form-group">
                                        <label>Select Streams</label>
                                        @foreach ($remaining_streams as $stream)
                                            <div class="form-check">
                                                <input type="checkbox" wire:model="streams_selected" value="{{ $stream->id }}"
                                                    class="form-check-input" id="stream_{{ $stream->id }}">
                                                <label class="form-check-label" for="stream_{{ $stream->id }}">
                                                    {{ $stream->schoolClass->name }} {{ $stream->alias }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            @endif
                        @endif

                        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled" wire:target="assign,update">
                            <span wire:loading.remove wire:target="assign,update">
                                {{ $isEdit ? 'Edit Subject Teacher' : 'Assign Subject Teacher' }}
                            </span>
                            <span wire:loading wire:target="assign,update">
                                <i class="fa fa-spinner fa-spin"></i> {{ $isEdit ? 'Updating...' : 'Assigning...' }}
                            </span>
                        </button>

                    </form>
                </div>
            </div>
        @endif
    @endif
    @if ($assignments->isNotEmpty())
        <div class="mt-4 card">
            <div class="card-header">
                <h4>{{ $class->name }} Subject Teachers </h4>

                <div class="card-header-form d-flex justify-content-between align-items-center">
                    <div class="mb-3 input-group">
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Search by teacher name">
                        <div wire:loading>
                            <span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
            
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Teacher Details</th>
                                <th>Subject</th>
                                @if (empty($this->class->teacher_class_id)) <th>Stream(s)</th> @endif
                                @role('academic teacher')
                                    <th>Actions</th>
                                @endrole
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $serialNumber = 1;
                            @endphp
                            @foreach ($assignments as $teacherId => $teacherAssignments)
                                @php
                                    $teacher = $teacherAssignments->first()->teacher;
                                    $uniqueSubjects = $teacherAssignments->pluck('subject')->unique('id');
                                    $firstSubject = $uniqueSubjects->shift(); 
                                @endphp
                                <tr>
                                    <td rowspan="{{ $uniqueSubjects->count() + 1 }}" style="vertical-align: top;">{{ $serialNumber++ }}</td>
                                    <td rowspan="{{ $uniqueSubjects->count() + 1 }}" style="vertical-align: top;">
                                        {{ $teacher->name }}
                                        <div class="text-muted" style="font-size: 0.85em;">
                                            {{ $teacher->phone ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        @if ($firstSubject)
                                            <a href="{{ route('subjects.show',['subject'=>$firstSubject->id]) }}">
                                                {{ $firstSubject->name }}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                    @if (empty($this->class->teacher_class_id))
                                        <td>
                                            @if ($firstSubject)
                                                @php
                                                    $streamsForFirstSubject = $assigned_teachers
                                                        ->where('subject_id', $firstSubject->id)
                                                        ->where('teacher_id', $teacher->id)
                                                        ->pluck('stream');
                                                @endphp
                                                @if ($streamsForFirstSubject->isNotEmpty())
                                                    @foreach ($streamsForFirstSubject as $index => $stream)
                                                        <span>{{ $stream->name }}
                                                            @if ($index == $streamsForFirstSubject->count() - 2)
                                                                &
                                                            @elseif (!$loop->last)
                                                                ,
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                @endif
                                            @endif
                                        </td>
                                    @endif
                                    @role('academic teacher')
                                        <td>
                                            @php
                                                $firstAssignmentForSubject = $teacherAssignments->where('subject_id', $firstSubject->id)->first();
                                            @endphp
                                            @if($firstAssignmentForSubject)
                                                <button wire:click="editAssignment({{ $firstAssignmentForSubject->id }},{{ $firstAssignmentForSubject->stream->id ?? 'null' }})"
                                                        class="btn btn-warning btn-sm">Edit</button>
                                                <button wire:click="confirmDelete({{ $firstAssignmentForSubject->id }},{{ $firstSubject->id }},{{ $firstAssignmentForSubject->stream->id ?? 'null' }})"
                                                        class="btn btn-danger btn-sm">Delete</button>
                                            @endif
                                        </td>
                                    @endrole
                                </tr>

                                @foreach ($uniqueSubjects as $subject)
                                    <tr>
                                        <td>
                                            <a href="{{ route('subjects.show',['subject'=>$subject->id]) }}">
                                                {{ $subject->name }}
                                            </a>
                                        </td>
                                        @if (empty($this->class->teacher_class_id))
                                            <td>
                                                @php
                                                    $streamsForCurrentSubject = $assigned_teachers
                                                        ->where('subject_id', $subject->id)
                                                        ->where('teacher_id', $teacher->id)
                                                        ->pluck('stream');
                                                @endphp
                                                @if ($streamsForCurrentSubject->isNotEmpty())
                                                    @foreach ($streamsForCurrentSubject as $index => $stream)
                                                        <span>{{ $stream->name }}
                                                            @if ($index == $streamsForCurrentSubject->count() - 2)
                                                                &
                                                            @elseif (!$loop->last)
                                                                ,
                                                            @endif
                                                        </span>
                                                    @endforeach
                                                @endif
                                            </td>
                                        @endif
                                        @role('academic teacher')
                                            <td>
                                                @php
                                                    $currentAssignmentForSubject = $teacherAssignments->where('subject_id', $subject->id)->first();
                                                @endphp
                                                @if($currentAssignmentForSubject)
                                                    <button wire:click="editAssignment({{ $currentAssignmentForSubject->id }},{{ $currentAssignmentForSubject->stream->id ?? 'null' }})"
                                                            class="btn btn-warning btn-sm">Edit</button>
                                                    <button wire:click="confirmDelete({{ $currentAssignmentForSubject->id }},{{ $subject->id }},{{ $currentAssignmentForSubject->stream->id ?? 'null' }})"
                                                            class="btn btn-danger btn-sm">Delete</button>
                                                @endif
                                            </td>
                                        @endrole
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>          
                    </table>
            </div>
        </div>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            window.addEventListener('scrollToForm', function () {
                const form = document.querySelector('#assignmentForm');
                if (form) {
                    window.scrollTo({ top: form.offsetTop, behavior: 'smooth' });
                }
            });
        });
    </script>
</div>
