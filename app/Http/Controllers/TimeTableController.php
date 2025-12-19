<?php

namespace App\Http\Controllers;

use App\Models\TimeTable;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Imports\TimetableImport;
use App\Exports\TimeTableTemplate;
use Maatwebsite\Excel\Facades\Excel;

class TimeTableController extends Controller
{
    /**
     * Display a listing of the resource.
     */

     public function downloadTemplate($classId)
{

    $schoolName =  SchoolClass::find($classId)->name;
    return Excel::download(new TimeTableTemplate($classId), 'timetable_template '.$schoolName.'.xlsx');
}
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function reset($classId)
    {


        $timeTables  = TimeTable::where('school_class_id',$classId)->get();
       if($timeTables){

        foreach($timeTables as $table){

            $table->delete();
        }
        return response()->json([
            'message'=>'reset succesful !'
        ],200);
       }

      return response()->json([
        'message'=>'resource not Found !'
       ],404);
     
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request,$classId)
    {
        // $request->validate([
        //     'input_method' => 'required',
        //     'day' => 'required_if:input_method,manual',
        //     'time' => 'required_if:input_method,manual',
        //     'stream_id' => 'required_if:input_method,manual',
        //     'timetable_file' => 'required_if:input_method,excel|mimes:xls,xlsx',
        // ]);



    
            Excel::import(new TimetableImport($classId), $request->file('timetable_file'));
    
            flash()->option('position','bottom-right')->success('Timetable saved successfully.');
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     */
    public function show($classId)
    {


        $timeTables = TimeTable::where('school_class_id', $classId)
        ->with(['subject', 'teacher', 'stream']) // Assuming relationships are set up
        ->get()
        ->groupBy('day');
    
    if ($timeTables->isNotEmpty()) {
        $formattedData = $timeTables->map(function ($schedules) {
            return $schedules->map(function ($schedule) {
                return [
                    'start_time' => $schedule->start_time,
                    'end_time' => $schedule->end_time,
                    'subject' => $schedule->subject->name,
                    'teacher' => $schedule->teacher->name,
                    'stream' => $schedule->stream->name,
                ];
            });
        });
    
        return response()->json($formattedData, 201);
    }
    
    return response()->json([
        'message' => 'Error'
    ], 402);
    
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(TimeTable $timeTable)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, TimeTable $timeTable)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TimeTable $timeTable)
    {
        //
    }
}
