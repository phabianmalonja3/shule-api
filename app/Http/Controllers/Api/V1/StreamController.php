<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\User;
use App\Models\Stream;
use App\Models\Teacher;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Models\StreamSubject;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\StreamCollection;

class StreamController extends Controller
{
    // List all streams for a class

    // Create a new stream for a class
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => ['nullable', 'exists:users,id'], // Optional, must exist in the users table
            'school_class_id' => 'required|exists:school_classes,id', // Ensure the class exists
        ]);

        DB::beginTransaction();
        try{
            $schoolClass = SchoolClass::find($validated['school_class_id']);

            $validated['name'] = strtoupper($validated['name']);
            // Ensure that the stream name is unique within the school class
            $existingStream = Stream::where('school_class_id', $validated['school_class_id'])->first();

            if($existingStream){
                if ($existingStream->name == $validated['name'] && !$schoolClass->teacher_class_id) {
                    DB::rollBack();
                    flash()->option('position', 'bottom-right')->error('A stream with this name already exists in this class.');
                    return back();
                }   
            }

            // Count existing streams in this class to generate an alias
            $existingStreamsCount = Stream::where('school_class_id', $validated['school_class_id'])->count();
            $alias = chr(65 + $existingStreamsCount); // Generate alias (A, B, C, etc.)

            if ($alias > 'Z') { // Limit streams to 26 (A-Z)
                DB::rollBack();
                flash()->option('position', 'bottom-right')->error('Exceeded the limit of streams for this class.');
                return back();
            }

            $validated['alias'] = $alias; // Add the alias to the validated data

            if ($schoolClass->teacher_class_id) {
                $existingStream->name = $validated['name'];
                $existingStream->save();

                $stream = $existingStream ?? [];
                $schoolClass->teacher_class_id = null; // Remove the existing teacher assignment
                $schoolClass->save();

            }else{
                $stream = Stream::create($validated);
            }  

            if($stream){
                if ($request->teacher_id) {

                    $user = User::findorfail($request->teacher_id);

                    // $stream->stream_teacher_id = $request->teacher_id;
                    $stream->teacher_id = $request->teacher_id;

                    $user->syncRoles(['class teacher']);
                    flash()->option('position', 'bottom-right')->success('New stream created and successfully assigned a class teacher');
                } else {
                    $stream->teacher_id = null;
                    flash()->option('position', 'bottom-right')->success('New stream created successfully');
                }

                $stream->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // Log the error for debugging
            Log::error('Error creating stream: ' . $e->getMessage());

            flash()->option('position', 'bottom-right')->error('An error occurred while creating the stream.' . $e->getMessage());
            return back();
        }

        return back();
    }

    public function update(Request $request, $id)
    {  
        $request->validate([
            'name' => 'required|string',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $stream = Stream::findorfail($id);
        $old_stream_teacher_id = $stream->teacher_id;
        $stream->update(['teacher_id' => $request->teacher_id, 'name' => $request->name]);
        // $stream->teacher_id = $request->teacher_id;
        // $stream->name = $request->name;
        //$stream->save();
            
        $user = User::findorfail($old_stream_teacher_id);
        $teacher_other_streams = Stream::where('teacher_id',$old_stream_teacher_id)->count();
        if($teacher_other_streams == 0 && !$user->hasAnyRole(['academic teacher','head teacher','header teacher','assistant headteacher'])){
            $user->syncRoles(['teacher']);
        }
        flash()->option('position', 'bottom-right')->success('Changes made successfully.');

        return redirect()->route('classes.show',['class'=>$stream->schoolClass->id]);
    }
    public function editStreamTeacher(Request $request, $id)
{

    $request->validate([
        'name' => 'required|string',
        'teacher_id' => 'required|exists:users,id',
    ]);

    $teacher = User::find($request->teacher_id);
    $stream = stream::findOrFail($id);
    $stream->teacher_id = $request->teacher_id;
    // $stream->stream_teacher_id = $request->teacher_id;
    $stream->save();
    $teacher->syncRoles(['class teacher']);
                flash()->option('position', 'bottom-right')->success('Stream Subject Assignment updated successfully!');

    return redirect()->route('classes.show',['class'=>$request->classId]);
}



    // Delete a stream
    public function destroy($streamId)
    {
        try {
            DB::beginTransaction();

            // Find the stream by ID
            $stream = Stream::findOrFail($streamId);

            // Find the next higher alias (if any)
            $nextHigherAlias = Stream::where('school_class_id', $stream->school_class_id)
                                     ->where('alias', '>', $stream->alias)
                                     ->orderBy('alias', 'asc')
                                     ->value('alias');

            // If a higher alias exists, prevent deletion
            if ($nextHigherAlias) {
                DB::rollBack();
                flash()->option('position', 'bottom-right')->error('Cannot delete lower stream . Delete  (' . $nextHigherAlias . ') first.');
                return back();
            }

            // Delete the stream
            $stream->delete();

            DB::commit();

            flash()->option('position', 'bottom-right')->success('Stream deleted successfully.');
            return back();

        } catch (\Exception $e) {
            DB::rollBack();
            flash()->option('position', 'bottom-right')->error('An error occurred while deleting the stream.');
            return back();
        }
    }
    public function edit(Stream $stream)
    {

        // Find the stream by ID
        $headTeacher = Auth::user(); // Get the authenticated user


        // dd($stream);
        // Define roles and get school ID
        $roles = ['teacher', 'class teacher', 'academic teacher'];
        $schoolId = $headTeacher->school_id;

        // Build the teacher query
        $teachersQuery = User::latest()->where('school_id', $schoolId)
            ->whereHas('roles', function ($query) use ($roles) {
                $query->whereIn('name', $roles);
            });

        $teachers = $teachersQuery->get();
        $teacher = $stream->teacher;

    return view('stream.teacher-stream-edit',compact('teacher','stream','teachers'));
    }




}

