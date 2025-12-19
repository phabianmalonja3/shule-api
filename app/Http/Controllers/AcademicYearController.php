<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AcademicYearController extends Controller
{
    /**
     * Display a listing of the resource.
     */
 
        public function index()
    {
        $academicYears = AcademicYear::paginate(10);
        return view('academic.index', compact('academicYears'));
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
    $validated = $request->validate([
        'start_year' => 'required|date|before:end_year',
        'end_year' => 'required|date|after:start_year',
        'midterm_start_date' => 'required|date|before:midterm_end_date',
        'midterm_end_date' => 'required|date|after:midterm_start_date',
        'annual_start_date' => 'required|date|before:annual_end_date',
        'annual_end_date' => 'required|date|after:annual_start_date',
        'description' => 'required|string',
    ]);

    try {
   
        
           $data = AcademicYear::create([
                'start_date'=>$validated['start_year'],
                 'end_date'=>$validated['end_year'],
                 'annual_start_date'=>$validated['annual_start_date'],
                 'annual_end_date'=>$validated['annual_end_date'],
                 'midterm_start_date'=> $validated['midterm_start_date'],
                 'midterm_end_date'=> $validated['midterm_end_date'],
               'description'=> $validated['description'],

            ]);
        
    
        return response()->json([
            'message' => 'Academic Year added successfully!',
           'data'=>$data
        ], 201);
    } catch (\Exception $e) {
        // Log the error for debugging
        Log::error('Error inserting academic year: ' . $e->getMessage());
    
        // Return an error response
        return response()->json([
            'message' => 'Error occurred while adding the academic year.',
            'error' => $e->getMessage()
        ], 500);
    }
    
}


    /**
     * Display the specified resource.
     */
    public function show(AcademicYear $academicYear)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AcademicYear $academicYear)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            // Validate the incoming request
            $request->validate([
                'start_year' => 'required|date',
                'end_year' => 'required|date',
                'midterm_start_date' => 'required|date',
                'midterm_end_date' => 'required|date',
                'annual_start_date' => 'required|date',
                'annual_end_date' => 'required|date',
                'description' => 'required|string',
            ]);
    
            // Find the academic year and update
            $academicYear = AcademicYear::findOrFail($id);
            $academicYear->update([
                'start_date' => $request->start_year,
                'end_date' => $request->end_year,
                'midterm_start_date' => $request->midterm_start_date,
                'midterm_end_date' => $request->midterm_end_date,
                'annual_start_date' => $request->annual_start_date,
                'annual_end_date' => $request->annual_end_date,
                'description' => $request->description,
            ]);
    
            // Return a success response
            return response()->json(['message' => 'Academic Year updated successfully']);
        } catch (\Exception $e) {
            // Log the error (optional)
            Log::error('Error updating academic year: ' . $e->getMessage());
    
            // Return a failure response
            return response()->json(['message' => 'Error updating academic year. Please try again later.'], 500);
        }
    }
    
    public function destroy(AcademicYear $academicYear)
    {
        //
    }
}
