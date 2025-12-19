<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Resources\StreamCollection;

class StreamSubjectTeacherController extends Controller
{
    /**
     * Assign a teacher to a subject in a stream.
     */
    public function store(Request $request)
    {
        $request->validate([
            'stream_id' => 'required|exists:streams,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id',
            'school_class_id' => 'required|exists:school_classes,id'
        ]);

        $stream = Stream::find($request->stream_id);
        $stream->subjectTeachers()->attach($request->teacher_id, ['subject_id' => $request->subject_id,'school_class_id'=>$request->school_class_id]);

        return response()->json(['message' => 'Teacher assigned successfully.', 'data'=>new \App\Http\Resources\Stream($stream)], 201);
    }
    public function index(Request $request)
    {
        $stream = Stream::where('school_class_id',$request->school_class_id)->get();

        return new StreamCollection($stream);
        // return response()->json(['message' => 'Teacher assigned successfully.'], 201);
    }

    /**
     * Update the teacher for a subject in a stream.
     */
    public function update(Request $request, $streamId)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id'
        ]);

        $stream = Stream::find($streamId);
        $stream->subjectTeachers()->syncWithoutDetaching([$request->teacher_id => ['subject_id' => $request->subject_id]]);

        return response()->json(['message' => 'Teacher reassigned successfully.']);
    }

    /**
     * Remove a teacher from a subject in a stream.
     */
    public function destroy(Request $request, $streamId)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:users,id'
        ]);

        $stream = Stream::find($streamId);
        $stream->subjectTeachers()->detach($request->teacher_id, ['subject_id' => $request->subject_id]);

        return response()->json(['message' => 'Teacher removed successfully.']);
    }


}
