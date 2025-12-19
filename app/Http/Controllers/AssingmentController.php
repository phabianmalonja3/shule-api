<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Models\StreamSubject;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;

class AssingmentController extends Controller
{
   
    public function index()
    {
        
        $assignments= Assignment::where('teacher_id',auth()->id())

        ->with(['subject', 'stream'])

        ->latest()

        ->paginate(10);
 
        return view('assignments.list');


    }

    public function create()
    {

        $teacher = Auth::user();

        $streamSubjects = StreamSubjectTeacher::with(['subject', 'stream'])

        ->where('teacher_id', $teacher->id)

        ->get();
    
        $subjects = $streamSubjects

        ->pluck('subject')

        ->filter()

        ->unique('id')

        ->values();

        // Ensure no duplicate subjects
        $streams = $streamSubjects->pluck('stream')->unique('id');  
        
       
        // Ensure no duplicate streams
    
        return view('assignments.create',compact('subjects','streams'));
    }
    public function studentAssignment()
    {

        
        return view('student.assignment.show');
    }

   
  
 
  

    public function show(string $id)
    {

        $assignment = Assignment::findorfail($id);

        return view('assignments.view',compact('assignment'));
    }


    public function edit(string $id)
    {
        $assignment = Assignment::findOrFail($id);
 

        return view('assignments.create',compact('assignment'));// Ensure no duplicate streams
    
    }

   
    public function update(Request $request, Assignment $assignment)
    {
        try{

            DB::beginTransaction();

            $request->validate(
                [
                'title' => 'required|string|max:255',

                'subject_id' => 'required|exists:subjects,id',

                'stream_id' => 'required|exists:streams,id',

                'file' => 'nullable|file|mimes:pdf,docx,doc|max:2048',

                'due_date' => 'required|date',
            ]
        );
        
            $assignment
            ->update(
                [
                'title' => $request->title,

                'subject_id' => $request->subject_id,

                'stream_id' => $request->stream_id,

                'due_date' => $request->due_date,
            ]);
        
            if ($request->hasFile('file')) {

                $assignment

                ->clearMediaCollection('assigmnets_files'); 

                $assignment

                ->addMedia($request->file('file'))

                ->toMediaCollection('assigmnets_files'); 
            }
            flash()

            ->option('position', 'bottom-right')

            ->success('Assigment updated successfully.');
        
            DB::commit();

            return redirect()

            ->route('assignments.index');
        }catch(\Exception $e)
        {
            DB::rollBack();

            flash()

            ->option('position', 'bottom-right')

            ->error('Error in updating  assignent Due to. ' .$e->getMessage());


        }
    }

    public function destroy(string $id)
    {
        $assigment  = Assignment::findorfail($id);

        $assigment->delete();

    flash()
    
    ->option('position', 'bottom-right')

    ->success('Assigment updated successfully.');

    return redirect()->route('assignments.index');

    }
}
