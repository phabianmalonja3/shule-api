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

class AssignTeacher extends Component
{

    public $school_class_id;
    public $teachers;
    public $stream_selected;
    public $streams;
    public $subjects;
    public $all_streams;
    public $streams_selected = [];
    public $teacher_id;
    public $assignment = null;
    public $subject_id;
    public $search;
    public $stream_id;
    public $editAssignmentId;
    public $isEdit = false;
    public $class = [];



    protected $rules = [
        'subject_id' => 'required|exists:subjects,id',
        'teacher_id' => 'required|exists:users,id',
        'school_class_id' => 'required|exists:school_classes,id',
        'streams_selected.*' => 'exists:streams,id',
    ];
    public function mount($class)
    {
        $this->class = $class;

        $schoolId = Auth::user()->school_id; // Get the current user's school_id
        $roles = ['teacher', 'class teacher', 'academic teacher', 'header teacher', 'assistant headteacher'];
        $this->subjects  = Subject::where('school_id', $schoolId)->get();


        // Get the teachers in the same school and with specific roles
        $this->teachers = User::latest()->where('school_id', $schoolId)
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            })
            ->get();

        $this->school_class_id = $class->id;
        $this->streams = $class->streams;
    }


    public function update($editAssignmentId)
    {

        $this->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'subject_id' => 'required|exists:subjects,id',
        ], [
            'subject_id.required' => 'The subject field is mandatory.',
            'subject_id.exists' => 'The selected subject is invalid.',
            'teacher_id.required' => 'The teacher field is mandatory.',
            'teacher_id.exists' => 'The selected teacher is invalid.',
        ]);

        try {
            // Start the transaction
            DB::beginTransaction();

            // Find the assignment based on the given subject and stream
            $assignment = StreamSubjectTeacher::find($editAssignmentId);

            if ($assignment) {
                // Perform the update
                $assignment->update([
                    'subject_id' => $this->subject_id,
                    'stream_id' => $this->stream_id,
                    'teacher_id' => $this->teacher_id,
                ]);
                $this->reset(['subject_id']);
            } else {
                // Handle case where no assignment was found (optional)

                flash()->option('position', 'bottom-right')->error('Assignment not found.');
                return back();
            }

            // Commit the transaction

            flash()->option('position', 'bottom-right')->success('Assignment updated successfully.');
            return redirect()->back();
            DB::commit();

            return back();
        } catch (\Exception $e) {
            // Rollback the transaction in case of error
            DB::rollBack();

            flash()->option('position', 'bottom-right')->error('Assignment update failed: ' . $e->getMessage());


            // Log the error for debugging
            Log::error('Assignment update failed: ' . $e->getMessage());


            flash()->option('position', 'bottom-right')->error('Failed to update the assignment.');

            return back();
        }
    }

    public function assign()
    {
        $this->validate();

        try {
            $schoolClass = SchoolClass::find($this->school_class_id);
            $schoolClass->subjects()->attach($this->subject_id);



            $streams = $this->all_streams == 'yes'
                ? Stream::where('school_class_id', $this->school_class_id)->get()
                : Stream::whereIn('id', $this->streams_selected)
                ->where('school_class_id', $this->school_class_id)
                ->get();



            $duplicateStreams = [];
            $conflictingStreams = [];

            DB::beginTransaction();

            foreach ($streams as $stream) {
                $conflictExists = StreamSubjectTeacher::where('subject_id', $this->subject_id)
                    ->where('stream_id', $stream->id)
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
            $this->resetInputFields();
        } catch (\Exception $e) {
            DB::rollback();
            // Log the error or handle it appropriately
            Log::error($e->getMessage());
            flash()->option('position', 'bottom-right')->error('An error occurred while assigning the subject teacher.' . $e->getMessage());
            return redirect()->back();
        }

        // Handle conflicting assignments
        if (!empty($conflictingStreams)) {
            $conflictStreamNames = implode(', ', $conflictingStreams);
            flash()->option('position', 'bottom-right')
                ->warning("The subject is already assigned to another teacher in the following streams: {$conflictStreamNames}.");
        }

        // Handle duplicate assignments
        if (!empty($duplicateStreams)) {
            $duplicateStreamNames = implode(', ', $duplicateStreams);
            flash()->option('position', 'bottom-right')
                ->warning("The subject is already assigned to this teacher in the following streams: {$duplicateStreamNames}.");
        }

        // Success message if new assignments were made
        if (count($duplicateStreams) + count($conflictingStreams) < count($streams)) {
            flash()->option('position', 'bottom-right')->success('Subject teacher assigned successfully.');
        }

        return redirect()->back();
    }

    public function resetInputFields()
    {
        $this->teacher_id = null;
        $this->subject_id = null;
        $this->all_streams = null;
        $this->streams_selected = [];
        $this->editAssignmentId = null;
    }

    public function confirmDelete($assignmentId, $subjectId, $streamId)
    {


        try {

            $assignment = StreamSubjectTeacher::find($assignmentId);

            if ($assignment) {
                // Validate that the assignment is linked to the given subject and stream
                if ($assignment->subject_id == $subjectId && $assignment->stream_id == $streamId) {
                    // Delete the assignment
                    $assignment->delete();


                    flash()->option('position', 'bottom-right')->success('Teacher removed from stream and subject.');
                    return back();
                }
            }

            flash()->option('position', 'bottom-right')->error('Assignment not found or does not match the provided IDs.');
            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            flash()->option('position', 'bottom-right')
                ->error("Error due to: " . $e->getMessage());
            return back();
        }
    }



    public function editAssignment($assignmentId, $streamId)
    {
        // Fetch the assignment by its ID
        $this->assignment = StreamSubjectTeacher::with(['stream', 'teacher', 'subject'])
            ->where('stream_id', $streamId)
            ->whereHas('stream', function ($query) {
                $query->where('school_class_id', $this->school_class_id);
            })
            ->find($assignmentId);


        // dd( $this->assignment);
        if ($this->assignment) {


            // dd($this->assignment);
            // Set the assignment ID for later use
            $this->editAssignmentId = $this->assignment->id;

            // Set the teacher ID and subject ID
            $this->teacher_id = $this->assignment->teacher_id;
            $this->stream_id = $this->assignment->stream_id;
            $this->subject_id = $this->assignment->subject_id;

            // $this->stream_id = $streamId;

            // dd($this->stream_id);

            $this->isEdit = true;

            // Dispatch the scroll event to bring the user to the form
            $this->dispatch('scrollToForm');
        }
    }

    public function getAvailableStreamsProperty()
    {
        return Stream::where('school_class_id', $this->school_class_id)
            ->whereDoesntHave('subjectTeachers', function ($query) {
                $query->where('subject_id', $this->subject_id);
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
                // Add search conditions to filter by teacher name, subject name, or stream name
                $query->whereHas('teacher', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                })
                    ->orWhereHas('subject', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('stream', function ($query) {
                        $query->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->get()
            ->groupBy('teacher_id');



        return view('livewire.teacher.assign-teacher', [
            'class' => $this->class,
            'assignments' => $assignments,
            'subjects' => $this->subjects
        ]);
    }
}
