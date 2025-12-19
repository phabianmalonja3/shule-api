<?php

namespace App\Livewire\Teacher;

use App\Models\User;
use App\Models\Stream;
use App\Models\Subject;
use App\Models\Teacher;
use Livewire\Component;
use App\Models\SchoolClass;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class AssignTeacher extends Component
{
    // Properties for the form
    public $school_class_id;
    public $subject_id;
    public $teacher_id;
    public $all_streams = null; // 'yes' or 'no' for new assignments
    public $streams_selected = []; // Array for selected streams (checkboxes)
    public $no_streams = null;

    // Properties for display and component state
    public $teachers;
    public $streams; // All streams for the current class
    public $subjects;
    public $search;
    public $isEdit = false;
    public $class; // The current SchoolClass model

    public $edit_assignment_id;
    private $initialAssignment;


    protected function rules()
    {
        $baseRules = [
            'subject_id' => ['required', 'exists:subjects,id'],
            'teacher_id' => ['required', 'exists:users,id'],
            'school_class_id' => 'required|exists:school_classes,id',
        ];

        if ($this->isEdit) {
            return array_merge($baseRules, [
                'streams_selected' => ['required', 'array', 'min:1', 'exists:streams,id'],
            ]);
        } else {

            return array_merge($baseRules, [
                // all_streams is still required, but we'll ensure its value
                'all_streams' => ['nullable'],
                'streams_selected' => [
                    'required_if:all_streams,no',
                    'array',
                    Rule::when($this->all_streams === 'no', ['min:1']),
                    'exists:streams,id',
                ],
            ]);

        }
    }

    // Custom validation messages (optional, but good practice)
    protected $messages = [
        'subject_id.required' => 'The subject field is mandatory.',
        'subject_id.exists' => 'The selected subject is invalid.',
        'teacher_id.required' => 'The teacher field is mandatory.',
        'teacher_id.exists' => 'The selected teacher is invalid.',
        'all_streams.required_if' => 'Please choose whether to apply to all streams or select specific ones.',
        'streams_selected.required_if' => 'Please select at least one stream, or choose to apply to all streams.',
        'streams_selected.min' => 'Please select at least one stream.',
        'streams_selected.*.exists' => 'One or more selected streams are invalid.',
    ];

    public function mount($class, $assignment_id = null)
    {
        $this->class = $class;
        $this->school_class_id = $class->id;

        $schoolId = Auth::user()->school_id;

        $this->subjects = Subject::where('school_id', $schoolId)->get();

        $roles = ['teacher', 'class teacher', 'academic teacher', 'header teacher', 'assistant headteacher'];
        $this->teachers = User::latest()->where('school_id', $schoolId)
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })
            ->get();

        $this->streams = $class->streams;

        if ($assignment_id) {
            $this->isEdit = true;
            $this->edit_assignment_id = $assignment_id;

            $this->initialAssignment = StreamSubjectTeacher::find($assignment_id);

            if ($this->initialAssignment) {
                $this->subject_id = $this->initialAssignment->subject_id;
                $this->teacher_id = $this->initialAssignment->teacher_id;

                $relatedAssignments = StreamSubjectTeacher::where('teacher_id', $this->initialAssignment->teacher_id)
                                                         ->where('subject_id', $this->initialAssignment->subject_id)
                                                         ->whereHas('stream', fn($q) => $q->where('school_class_id', $this->class->id))
                                                         ->get();

                $this->streams_selected = $relatedAssignments->pluck('stream_id')->toArray();
                $this->all_streams = null; // Ensure null in edit mode
            } else {
                session()->flash('error', 'Assignment not found for editing.');
                $this->isEdit = false;
                $this->edit_assignment_id = null;
                $this->resetInputFields();
            }
        }
    }

    public function updated($propertyName)
    {
        Log::info("updated() called. Property: " . $propertyName . 
                   ", isEdit: " . ($this->isEdit ? 'true' : 'false') .
                   ", Current all_streams: " . $this->all_streams . 
                   ", Current streams_selected: " . json_encode($this->streams_selected));

        if ($propertyName === 'all_streams') {
            if ($this->all_streams === 'yes') {

                $this->streams_selected = [];
                Log::info("streams_selected cleared due to all_streams = yes: " . json_encode($this->streams_selected));

            } elseif ($this->all_streams === 'no') {
                // Do nothing specific here, user is explicitly choosing 'no'
            } else {
                // If all_streams becomes null/empty (e.g., user selects "Select option" again)
                // We should also clear streams_selected to avoid confusion.
                $this->streams_selected = [];
            }
        }

        // --- NEW LOGIC HERE ---
        // If a stream checkbox is checked/unchecked, and we are not in edit mode,
        // automatically set all_streams to 'no' if it's not already 'yes'.
        if ($propertyName === 'streams_selected' && !$this->isEdit) {
            if ($this->all_streams !== 'yes') {
                $this->all_streams = 'no';
                Log::info("all_streams set to 'no' because streams_selected changed.");
            }
        }
        // --- END NEW LOGIC ---

        if ($propertyName === 'subject_id') {
            $this->streams_selected = [];
            $this->all_streams = null; // Reset all_streams when subject changes
            Log::info("Streams and all_streams reset due to subject change. Streams_selected: " . json_encode($this->streams_selected) . ", All_streams: " . $this->all_streams);
        }

        $this->validateOnly($propertyName, $this->rules());
    }

    public function update()
    {
        Log::info('Assign method called. isEdit: ' . ($this->isEdit ? 'true' : 'false') . ', all_streams: ' . $this->all_streams . ', streams_selected: ' . json_encode($this->streams_selected));

        $this->validate($this->rules());

        try {
            DB::beginTransaction();

            $existingAssignments = StreamSubjectTeacher::where('teacher_id', $this->teacher_id)
                ->where('subject_id', $this->subject_id)
                ->whereHas('stream', fn($query) => $query->where('school_class_id', $this->school_class_id))
                ->get();

            $previouslyAssignedStreamIds = $existingAssignments->pluck('stream_id')->toArray();

            $streamsToRemove = array_diff($previouslyAssignedStreamIds, $this->streams_selected);
            $streamsToAdd = array_diff($this->streams_selected, $previouslyAssignedStreamIds);

            if (!empty($streamsToRemove)) {
                StreamSubjectTeacher::where('teacher_id', $this->teacher_id)
                    ->where('subject_id', $this->subject_id)
                    ->whereIn('stream_id', $streamsToRemove)
                    ->delete();
            }

            foreach ($streamsToAdd as $streamId) {
                $conflict = StreamSubjectTeacher::where('subject_id', $this->subject_id)
                    ->where('stream_id', $streamId)
                    ->where('teacher_id', '!=', $this->teacher_id)
                    ->first();

                if ($conflict) {
                    // DB::rollBack();
                    // flash()->option('position', 'bottom-right')->error('Cannot update: Subject is already assigned to another teacher in stream ID: ' . $streamId);
                    // return back();
                    $conflict->update(['teacher_id' => $this->teacher_id]);
                }else{
                    StreamSubjectTeacher::create([
                        'stream_id' => $streamId,
                        'school_class_id' => $this->school_class_id,
                        'subject_id' => $this->subject_id,
                        'teacher_id' => $this->teacher_id,
                    ]);
                }
            }

            foreach ($existingAssignments as $assignment) {
                if (in_array($assignment->stream_id, $this->streams_selected)) {
                    $assignment->update([
                        'subject_id' => $this->subject_id,
                        'teacher_id' => $this->teacher_id,
                    ]);
                }
            }

            DB::commit();

            flash()->option('position', 'bottom-right')->success('Assignments updated successfully!');
            $this->resetInputFields();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Assignment update failed: ' . $e->getMessage());
            flash()->option('position', 'bottom-right')->error('Failed to update the assignment: ' . $e->getMessage());
        }

        return back();
    }

    public function updatedSubjectId($value)
    {
        // Reset stream selections when the subject changes
        $this->streams_selected = [];
        $this->all_streams = null;

        // Get the streams not yet assigned to the newly selected subject
        $assigned_streams_for_selected_subject = StreamSubjectTeacher::where('subject_id', $value)
            ->whereHas('stream', fn($q) => $q->where('school_class_id', $this->school_class_id))
            ->pluck('stream_id')
            ->unique();
            
        $remaining_streams = $this->streams->filter(function($stream) use ($assigned_streams_for_selected_subject) {
            return !$assigned_streams_for_selected_subject->contains($stream->id);
        });

        // If there's only one stream left, automatically select it and apply the assignment
        if ($remaining_streams->count() === 1) {
            $this->streams_selected = [$remaining_streams->first()->id];
            $this->all_streams = 'yes'; // Set the flag to handle this scenario
        }
    }

    public function assign()
    {
        Log::info('Assign method called. isEdit: ' . ($this->isEdit ? 'true' : 'false') . ', all_streams: ' . $this->all_streams . ', streams_selected: ' . json_encode($this->streams_selected));

        if (!$this->isEdit) {
            // If neither 'yes' nor 'no' is selected AND no streams are checked (default state)
            if (is_null($this->all_streams) && empty($this->streams_selected)) {
                $this->all_streams = null; // Ensure it is null
                
                // Manually set an error for the 'all_streams' field
                $this->addError('all_streams', 'Please choose whether to apply to all streams or select specific ones.');
                return; // Stop execution
            }
            
            // If streams are selected but the radio button isn't set (due to user flow error), force it to 'no'
            if (is_null($this->all_streams) && !empty($this->streams_selected)) {
                $this->all_streams = 'no';
            }
        }

        $this->validate($this->rules());

        try {
            DB::beginTransaction();

            $streamsToAssign = collect();
            if ($this->all_streams === 'yes') {
                // This covers both the "Apply to all" option and the new single-stream auto-selection
                if ($this->streams_selected) {
                    $streamsToAssign = Stream::whereIn('id', $this->streams_selected)->get();
                } else {
                     $streamsToAssign = Stream::where('school_class_id', $this->school_class_id)->get();
                }
            } elseif ($this->all_streams === 'no' && !empty($this->streams_selected)) {
                $streamsToAssign = Stream::whereIn('id', $this->streams_selected)
                    ->where('school_class_id', $this->school_class_id)
                    ->get();
            }

            $duplicateStreams = [];
            $conflictingStreams = [];

            foreach ($streamsToAssign as $stream) {
                $conflictExists = StreamSubjectTeacher::where('subject_id', $this->subject_id)
                    ->where('stream_id', $stream->id)
                    ->where('teacher_id', '!=', $this->teacher_id)
                    ->exists();

                if ($conflictExists) {
                    $conflictingStreams[] = $stream->name;
                } else {
                    $alreadyAssigned = StreamSubjectTeacher::where('subject_id', $this->subject_id)
                        ->where('teacher_id', $this->teacher_id)
                        ->where('stream_id', $stream->id)
                        ->exists();

                    if (!$alreadyAssigned) {
                        StreamSubjectTeacher::create([
                            'stream_id' => $stream->id,
                            'school_class_id' => $this->school_class_id,
                            'subject_id' => $this->subject_id,
                            'teacher_id' => $this->teacher_id,
                        ]);
                    } else {
                        $duplicateStreams[] = $stream->name;
                    }
                }
            }

            DB::commit();

            $successCount = count($streamsToAssign) - count($conflictingStreams) - count($duplicateStreams);
            if ($successCount > 1) {
                flash()->option('position', 'bottom-right')->success('Subject teacher assigned successfully to ' . $successCount . ' stream(s).');
            }else{
                flash()->option('position', 'bottom-right')->success('Subject teacher assigned successfully.');
            }

            if (!empty($conflictingStreams)) {
                $conflictStreamNames = implode(', ', $conflictingStreams);
                flash()->option('position', 'bottom-right')
                    ->warning("The subject is already assigned to another teacher in: {$conflictStreamNames}.");
            }

            if (!empty($duplicateStreams)) {
                $duplicateStreamNames = implode(', ', $duplicateStreams);
                flash()->option('position', 'bottom-right')
                    ->warning("The subject is already assigned to this teacher in: {$duplicateStreamNames}.");
            }

            $this->resetInputFields();
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Assignment failed: ' . $e->getMessage());
            flash()->option('position', 'bottom-right')->error('An error occurred while assigning the subject teacher: ' . $e->getMessage());
        }

        return redirect()->back();
    }

    public function resetInputFields()
    {
        $this->teacher_id = null;
        $this->subject_id = null;
        $this->all_streams = null;
        $this->streams_selected = [];
        $this->isEdit = false;
        $this->edit_assignment_id = null;
        $this->initialAssignment = null;
    }

    public function confirmDelete($assignmentId, $subjectId, $streamId)
    {
        try {
            DB::beginTransaction();

            $assignment = StreamSubjectTeacher::find($assignmentId);

            if ($assignment) {
                if ($assignment->subject_id == $subjectId && $assignment->stream_id == $streamId) {
                    $assignment->delete();
                    DB::commit();
                    flash()->option('position', 'bottom-right')->success('Teacher removed from stream and subject.');
                } else {
                    DB::rollBack();
                    flash()->option('position', 'bottom-right')->error('Assignment not found or does not match the provided IDs.');
                }
            } else {
                DB::rollBack();
                flash()->option('position', 'bottom-right')->error('Assignment not found.');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting assignment: " . $e->getMessage());
            flash()->option('position', 'bottom-right')->error("Error deleting assignment: " . $e->getMessage());
        }
        return back();
    }

    public function editAssignment($assignmentId)
    {
        $this->initialAssignment = StreamSubjectTeacher::find($assignmentId);

        if ($this->initialAssignment) {
            $this->isEdit = true;
            $this->edit_assignment_id = $this->initialAssignment->id;

            $this->teacher_id = $this->initialAssignment->teacher_id;
            $this->subject_id = $this->initialAssignment->subject_id;

            $relatedAssignments = StreamSubjectTeacher::where('teacher_id', $this->initialAssignment->teacher_id)
                                                     ->where('subject_id', $this->initialAssignment->subject_id)
                                                     ->whereHas('stream', fn($q) => $q->where('school_class_id', $this->class->id))
                                                     ->get();

            $this->streams_selected = $relatedAssignments->pluck('stream_id')->toArray();
            $this->all_streams = null; // Ensure null or empty string in edit mode
            $this->dispatch('scrollToForm');
        } else {
            session()->flash('error', 'Assignment not found for editing.');
            $this->isEdit = false;
            $this->edit_assignment_id = null;
            $this->resetInputFields();
        }
    }

    public function getAssignedTeachersProperty()
    {
        return StreamSubjectTeacher::with(['stream', 'teacher', 'subject'])
            ->whereHas('stream', function ($query) {
                $query->where('school_class_id', $this->school_class_id);
            })
            ->get();
    }

    public function render()
    {
        $assignments = StreamSubjectTeacher::with(['stream', 'teacher', 'subject'])
            ->whereHas('stream', function ($query) {
                $query->where('school_class_id', $this->school_class_id);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('teacher', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('subject', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('stream', function ($subQuery) {
                        $subQuery->where('name', 'like', '%' . $this->search . '%');
                    });
                });
            })
            ->get()
            ->groupBy('teacher_id');

        $assigned_teachers_for_subject_filter = $this->getAssignedTeachersProperty();


        return view('livewire.teacher.assign-teacher', [
            'class' => $this->class,
            'assignments' => $assignments,
            'subjects' => $this->subjects,
            'assigned_teachers' => $assigned_teachers_for_subject_filter,
        ]);
    }
}

