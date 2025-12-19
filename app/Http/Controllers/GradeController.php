<?php

namespace App\Http\Controllers;

use App\Models\GradeScale;
use App\Models\GenericGrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function index()
    {

        $teacher =  auth()->user();

        $school_type =   $teacher->school->school_type;
        $school_type = json_decode($school_type);





        $grades = GenericGrade::whereIn('school_type', $school_type)
            ->get()
            ->groupBy('school_type');

        $gradessScale = GradeScale::where('school_id', $teacher->school_id)->get()
            ->groupBy('school_type');

        return view('grades.index', compact('grades', 'gradessScale'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        try {

            $existingGrade = GradeScale::where('grade', $request->grade)
                ->where('school_type', $request->school_type)
                ->where('school_id', auth()->user()->school_id)  // Exclude the current grade from the check
                ->first();

            // If an existing grade is found, return an error
            if ($existingGrade) {
                return response()->json([
                    'success' => false,
                    'message' => 'The grade already exists for this school type.'
                ], 400); // 400 Bad Request
            }


            if ($request->has('all_grade')) {

                $school_type = $request->school_type;
                $gradeScales = $this->gradeScales();
                foreach ($gradeScales as $grade) {
                    if ($grade['school_type'] == $school_type) {
                        $grade['school_id'] = auth()->user()->school_id;
                        DB::table('grade_scales')->insert($grade);
                    }
                }
            } else {

                $validated = $request->validate([
                    'grade' => 'required|max:1|uppercase|unique:grade_scales,grade',
                    'min_marks' => 'required|numeric|min:0|max:100',
                    'max_marks' => 'required|numeric|min:0|max:100',
                    'remarks' => 'required|string|max:255',
                    'school_type' => 'required'
                ]);

                $validated['school_id'] = auth()->user()->school_id;
                GradeScale::create($validated);
            }

            return response()->json(['success' => true, 'message' => 'Grade created successfully.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to create grade. ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function edit(GenericGrade $grade)
    {
        // Retrieve confirmed grades based on school ID
        $confirmedGrade = GradeScale::where('school_id', auth()->user()->school_id)
            ->where('school_type',$grade->school_type)
            ->where('grade',$grade->grade)
            ->latest()
            ->first();
        return view('grades.create', ['grade' => $grade, 'confirmedGrade' => $confirmedGrade]);
    }
    
    public function editGrade(GradeScale $grade)
    {

        return view('grades.edit', ['grade' => $grade]);
    }
    private function gradeScales()
    {

        return   [
            // For Primary School
            [
                'grade' => 'A',
                'min_marks' => 90.00,
                'max_marks' => 100.00,
                'remarks' => 'Excellent',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'B',
                'min_marks' => 75.00,
                'max_marks' => 89.99,
                'remarks' => 'Very Good',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'C',
                'min_marks' => 60.00,
                'max_marks' => 74.99,
                'remarks' => 'Good',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'D',
                'min_marks' => 50.00,
                'max_marks' => 59.99,
                'remarks' => 'average',
                'school_type' => 'primary',
            ],
            [
                'grade' => 'F',
                'min_marks' => 0.00,
                'max_marks' => 49.99,
                'remarks' => 'Fail',
                'school_type' => 'primary',
            ],


            [
                'grade' => 'A',
                'min_marks' => 85.00,
                'max_marks' => 100.00,
                'remarks' => 'Excellent',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'B',
                'min_marks' => 70.00,
                'max_marks' => 84.99,
                'remarks' => 'Very Good',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'C',
                'min_marks' => 55.00,
                'max_marks' => 69.99,
                'remarks' => 'Average',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'D',
                'min_marks' => 40.00,
                'max_marks' => 54.99,
                'remarks' => 'Below Average',
                'school_type' => 'O-level',
            ],
            [
                'grade' => 'F',
                'min_marks' => 0.00,
                'max_marks' => 39.99,
                'remarks' => 'Fail',
                'school_type' => 'O-level',
            ],

            // A-level
            [
                'grade' => 'A',
                'min_marks' => 85.00,
                'max_marks' => 100.00,
                'remarks' => 'Excellent',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'B',
                'min_marks' => 70.00,
                'max_marks' => 84.99,
                'remarks' => 'Very Good',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'C',
                'min_marks' => 55.00,
                'max_marks' => 69.99,
                'remarks' => 'Average',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'D',
                'min_marks' => 40.00,
                'max_marks' => 54.99,
                'remarks' => 'Below Average',
                'school_type' => 'A-level',
            ],
            [
                'grade' => 'F',
                'min_marks' => 0.00,
                'max_marks' => 39.99,
                'remarks' => 'Fail',
                'school_type' => 'A-level',
            ],
        ];
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        try {

            // $existingGrade = GradeScale::where('grade', $request->grade)
            //     ->where('school_type', $request->school_type)
            //     ->where('school_id', auth()->user()->school_id)  // Exclude the current grade from the check
            //     ->first();

            // If an existing grade is found, return an error
            // if ($existingGrade) {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'The grade already exists for this school type.'
            //     ], 400); // 400 Bad Request
            // }


            // $validated = $request->validate([
            //     'grade' => 'required|max:1',
            //     'min_marks' => 'required|numeric|min:0|max:100',
            //     'max_marks' => 'required|numeric|min:0|max:100',
            //     'remarks' => 'nullable|string|max:255',
            // ]);

            // $grades = GradeScale::where('school_id', auth()->user()->school_id)
            // ->where('school_type',$request->school_type)
            // ->where('grade','!=',$request->grade)
            // ->get();

            $confirmedGrades = GradeScale::select('grade', 'min_marks', 'max_marks')
                ->where('school_id', auth()->user()->school_id)
                ->where('school_type', $request->school_type)
                ->where('grade', '!=', $request->grade) 
                ->orderBy('created_at', 'desc')
                ->get()
                ->unique('grade');
                
            $gradesForComparison = null;

   
            $genericGrades = GenericGrade::select('grade', 'min_marks', 'max_marks')
                ->where('school_type', $request->school_type)
                ->where('grade', '!=', $request->grade) 
                ->get();
            
            if(count($confirmedGrades) == 0){
                $gradesForComparison = collect($genericGrades);
                $genericGrades = null;

            }elseif(count($confirmedGrades) < count($genericGrades)){
                $confirmedGradesByGrade = $confirmedGrades->keyBy('grade');
                $genericGradesByGrade = $genericGrades->keyBy('grade');
                $gradesForComparison = $confirmedGradesByGrade->union($genericGradesByGrade)->values(); 
                $genericGrades = $confirmedGrades = null;

            }else{
                $gradesForComparison = collect($confirmedGrades);
                $genericGrades = $confirmedGrades = null;

            }

            $validated = $request->validate([
                'grade' => 'required|max:1',
                'max_marks' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:100',
                    'gt:min_marks',
                    function ($attribute, $value, $fail) use ($gradesForComparison, $request) {
                        
                        $lowerGrade = $gradesForComparison
                            ->where('grade', '<', $request->grade)
                            ->sortByDesc('grade')
                            ->first();
                            
                        if ($lowerGrade && $value >= $lowerGrade->min_marks) {
                            $fail("Maximum marks should not be equal or greater than the minimum marks of grade '{$lowerGrade->grade}' ({$lowerGrade->min_marks}).");
                        }
                    },
                ],
                'min_marks' => [
                    'required',
                    'numeric',
                    'min:0',
                    'max:100',
                    function ($attribute, $value, $fail) use ($gradesForComparison, $request) {
                        $higherGrade = $gradesForComparison
                            ->where('grade', '>', $request->grade)
                            ->sortBy('grade')
                            ->first();

                        if ($higherGrade && $value <= $higherGrade->max_marks) {
                            $fail("Minimum marks should not be equal or less than the maximum marks of grade '{$higherGrade->grade}' ({$higherGrade->max_marks}).");
                        }
                    },
                ],
                'remarks' => 'nullable|string|max:255',
            ]);
    // 'grade' => [
    //     'required',
    //     'max:1',
    //     Rule::unique('grade_scales')
    //         ->where(function ($query) use ($request) {
    //             return $query->where('school_id', auth()->user()->school_id)
    //                          ->where('school_type', $request->school_type)
    //                          ->where('created_at', function ($subquery) use ($request) {
    //                              // This subquery ensures we only check uniqueness against the latest grade for each grade name.
    //                              $subquery->select(DB::raw('MAX(created_at)'))
    //                                       ->from('grade_scales')
    //                                       ->whereColumn('school_id', 'grade_scales.school_id')
    //                                       ->whereColumn('school_type', 'grade_scales.school_type')
    //                                       ->groupBy('grade');
    //                          });
    //         })
    //         // If you are editing an existing record, you'd use this `ignore` method.
    //         // ->ignore($request->route('gradeScaleId'))
    //],
//     'remarks' => 'nullable|string|max:255',
// ]);
            DB::beginTransaction();
            $validated['school_id'] = auth()->user()->school_id;
            $validated['grade'] = strtoupper($validated['grade']);
            $validated['school_type'] = $request->school_type;
            GradeScale::create($validated);

            DB::commit();
            flash()->option('position', 'bottom-right')->success('Grade created successfully.');
            return redirect()->route('grades.index');
        } catch (\Exception $e) {

            flash()->option('position', 'bottom-right')->error('Failed to create grade. ' . $e->getMessage());
            return back();
        }
    }
    public function UpdateGrade(Request $request, GradeScale $grade)
    {


        try {

            $validated = $request->validate([
                'grade' => 'required|max:1',
                'min_marks' => 'required|numeric|min:0|max:100',
                'max_marks' => 'required|numeric|min:0|max:100',
                'remarks' => 'nullable|string|max:255',
            ]);
            DB::beginTransaction();
            $validated['school_id'] = auth()->user()->school_id;
            $validated['grade'] = strtoupper($validated['grade']);
            $grade->grade =     $validated['grade'];
            $grade->min_marks =     $validated['min_marks'];
            $grade->max_marks =     $validated['max_marks'];
            $grade->remarks =     $validated['remarks'];
            $grade->save();

            DB::commit();
            flash()->option('position', 'bottom-right')->success('Grade created successfully.');
            return redirect()->route('grades.index');
        } catch (\Exception $e) {

            flash()->option('position', 'bottom-right')->error('Failed to create grade. ' . $e->getMessage());
            return back();
        }
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GradeScale $grade)
    {
        $grade->delete();
        flash()->option('position', 'bottom-right')->success('Grade deleted successfully.');

        return redirect()->route('grades.index')->with('success', 'Grade deleted successfully.');
    }
}
