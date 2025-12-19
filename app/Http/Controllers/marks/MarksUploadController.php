<?php

namespace App\Http\Controllers\marks;

use App\Models\School;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MarksUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

         $school= auth()->user()->school;
        return view ('marks.marks_upload',compact('school'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
      public function store(Request $request)
{
    $request->validate([
        'upload_by' => 'required|in:academic,subject',
        'school_id' => 'required|exists:schools,id',
    ]);

    $school = School::findOrFail($request->school_id);
    $school->is_teacher_upload = $request->upload_by === 'academic' ? '0' : '1';
    $school->save();

    

    return response()->json(['message' => 'Upload permission updated successfully.']);

}

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
