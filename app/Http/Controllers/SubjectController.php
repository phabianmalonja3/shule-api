<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\SubjectResource;
use App\Http\Resources\SubjectCollection;
use App\Models\Combination;
use App\Models\School;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
   /**
     * Store a newly created subject in storage.
     */
    public function store(Request $request)
    {

        // dd($request);
        $request->validate([
            'name' => 'required|string|unique:subjects,name'
        ]);


        

        $subject = Subject::create(['name' => $request->name,'school_id'=>auth()->user()->school_id]);

        return redirect()->route('subjects.index');
    }
    public function create()
    {

        $student = [];
       

        return view('subjects.create',['subject'=>$student]);
    }

public function addCombination(Request $request)
{
    $request->validate([
        'combination_id' => 'required|exists:combinations,id',
        'subjects'       => 'nullable|array',
        'subjects.*'     => 'exists:subjects,id',
    ]);

    $combinationId = (int) $request->combination_id;
    $user = Auth::user();

    if (!$user || !$user->school_id || !$school = $user->school) {
        return back()->with('error', 'Associated school not found.');
    }

    // 1. Fetch predefined subject IDs from JSON pivot table
    $pivotRecord = DB::table('combination_subject')
        ->where('combination_id', $combinationId)
        ->first();

    $predefinedSubjectIds = [];
    if ($pivotRecord && !empty($pivotRecord->subject_id)) {
        $predefinedSubjectIds = is_array($pivotRecord->subject_id) 
            ? $pivotRecord->subject_id 
            : (json_decode($pivotRecord->subject_id, true) ?? []);
    }

    // 2. Combine predefined IDs + Extra user-selected subject IDs
    $extraSubjectIds = array_map('intval', $request->input('subjects', []));
    
    $allSubjectIdsToAttach = array_values(array_unique(array_merge(
        array_map('intval', $predefinedSubjectIds), 
        $extraSubjectIds
    )));

    // Execute database modifications inside transaction
    DB::transaction(function () use ($school, $combinationId, $extraSubjectIds, $user) {
        
        // A. Update School combinations array
        $currentSchoolCombinations = is_array($school->combinations) 
            ? $school->combinations 
            : (json_decode($school->combinations ?? '[]', true) ?? []);

        if (!in_array($combinationId, $currentSchoolCombinations, true)) {
            $currentSchoolCombinations[] = $combinationId;
            $school->update(['combinations' => array_values($currentSchoolCombinations)]);
        }

        // B. Categorize IDs: Belonging to this school vs General/External subjects
        $extraSchoolSubjectIds = Subject::whereIn('id', $extraSubjectIds)
            ->where('school_id', $user->school_id)
            ->pluck('id')
            ->toArray();

        // Calculate general/external IDs by finding the difference
        $extraGeneralSubjectIds = array_values(array_diff($extraSubjectIds, $extraSchoolSubjectIds));

        // C. Record extra general subjects in combination_extras table (upsert to prevent duplicates)
        if (!empty($extraGeneralSubjectIds)) {
            DB::table('combination_extras')->updateOrInsert(
                [
                    'school_id'      => $user->school_id,
                    'combination_id' => $combinationId,
                ],
                [
                    'subject_id'     => json_encode($extraGeneralSubjectIds),
                ]
            );
        }

        // D. Update Subject combination_id arrays for school-owned extra subjects
        if (!empty($extraSchoolSubjectIds)) {
            $subjects = Subject::whereIn('id', $extraSchoolSubjectIds)->get();

            foreach ($subjects as $subject) {
                $currentSubjectCombinations = is_array($subject->combination_id) 
                    ? $subject->combination_id 
                    : (json_decode($subject->combination_id ?? '[]', true) ?? []);

                if (!in_array($combinationId, $currentSubjectCombinations, true)) {
                    $currentSubjectCombinations[] = $combinationId;
                    $subject->update([
                        'combination_id' => array_values($currentSubjectCombinations),
                    ]);
                }
            }
        }
    });

    flash()->option('position', 'bottom-right')->success('Combination added successfully.');

    return back();
}
	
	public function getSubjects($id)
	{
		$schoolId = Auth::user()->school_id;
		$school = School::find($schoolId);
		$combination = Combination::with('subjects')->findOrFail($id);
		$generalSubjects    = ['English Language','Business Studies','Historia ya Tanzania na Maadili','Kiswahili','Basic Mathematics','Geography'];
		$allSubjects = $school->subjects()->whereNotIn('name',$generalSubjects)->orderBy('name')->get();
		$assignedIds = $combination->subjects->pluck('id')->toArray();

		return response()->json([
			'allSubjects' => $allSubjects,
			'assignedIds' => $assignedIds
		]);
	}

	public function updateCombination(Request $request)
	{
		$request->validate([
			'combination_id' => 'required|exists:combinations,id',
			'subjects' => 'nullable|array',
		]);

		$combination = Combination::findOrFail($request->combination_id);

		$generalSubjectNames = [
			'English Language', 'Business Studies', 'Historia ya Tanzania na Maadili', 
			'Kiswahili', 'Basic Mathematics', 'Geography'
		];
		
		$generalSubjectIds = Subject::whereIn('name', $generalSubjectNames)->pluck('id')->toArray();
		$currentAssignedIds = $combination->subjects->pluck('id')->toArray();
		$newSelectedIds = $request->subjects ?? [];
		$toRemoveIds = array_diff($currentAssignedIds, $newSelectedIds);
		$filteredRemovalIds = array_diff($toRemoveIds, $generalSubjectIds);

		if (!empty($filteredRemovalIds)) {
			$combination->subjects()->detach($filteredRemovalIds);
		}

		$combination->subjects()->syncWithoutDetaching($newSelectedIds);
		
        flash()->option('position', 'bottom-right')->success('Combination updated successfully.');

        return back();
	}

public function deleteCombination(Request $request)
{
    $request->validate([
        'combination_id' => 'required|exists:combinations,id',
    ]);

    $combinationId = (int) $request->combination_id;
    $user = Auth::user();

    if (! $user->school_id || ! $school = $user->school) {
        return back()->with('error', 'Associated school not found.');
    }

    $combination = Combination::findOrFail($combinationId);

    // Prevent deletion if students are associated
    if ($combination->students()->exists()) {
        return back()->with('error', 'Cannot delete because students are enrolled in this combination.');
    }

    DB::transaction(function () use ($school, $combinationId) {
        // 1. Remove combination ID from School's JSON array
        $schoolCombinations = is_array($school->combinations) 
            ? $school->combinations 
            : json_decode($school->combinations ?? '[]', true);

        $updatedSchoolCombinations = array_values(
            array_filter($schoolCombinations, fn($id) => (int)$id !== $combinationId)
        );

        $school->update(['combinations' => $updatedSchoolCombinations]);

        // 2. Remove combination ID from JSON arrays in Subjects table
        // Handles Laravel JSON array queries (e.g., JSON_CONTAINS)
        $subjects = Subject::whereJsonContains('combination_id', $combinationId)->get();

        foreach ($subjects as $subject) {
            $subjectCombinations = is_array($subject->combination_id)
                ? $subject->combination_id
                : json_decode($subject->combination_id ?? '[]', true);

            $updatedSubjectCombinations = array_values(
                array_filter($subjectCombinations, fn($id) => (int)$id !== $combinationId)
            );

            $subject->update(['combination_id' => $updatedSubjectCombinations]);
        }
    });

    flash()->option('position', 'bottom-right')->success('Combination deleted successfully.');

    return back();
}
	



public function updateSchoolSubjects(Request $request)
{
    // 1. Validate that the subject exists in the subjects table and the new name is filled
    $request->validate([
        'subject_id'   => 'required|exists:subjects,id',
        'subject_name' => 'required|string|max:255',
    ]);

    // 2. Secure the query to the logged-in user's school
    $schoolId = Auth::user()->school_id;

    $subject = Subject::where('id', $request->subject_id)
                      ->where('school_id', $schoolId)
                      ->first();

    // 3. Fallback check if someone tampered with the ID or it doesn't belong to this school
    if (!$subject) {
        flash()->option('position', 'bottom-right')->error('Subject not found or unauthorized.');
        return back();
    }

    // 4. Update the subject name with the new text string
    $subject->update([
        'name' => $request->subject_name
    ]);
    
    flash()->option('position', 'bottom-right')->success('Subject updated successfully.');

    return back();
}


    public function deleteSchoolSubjects(Request $request)
    {
        $request->validate([
            'subject_id'   => 'required|array',
            'subject_id.*' => 'exists:subjects,id'
        ]);

        $schoolId = Auth::user()->school_id;

        $subjectsQuery = Subject::whereIn('id', $request->subject_id)
                                ->where('school_id', $schoolId);

        $hasStudents = Combination::whereHas('subjects', function($q) use ($request) {
                            $q->whereIn('id', $request->subject_id);
                    })->whereHas('students')->exists();

        if ($hasStudents) {
            return back()->with('error', 'Cannot delete because students are enrolled in combinations using these subjects.');
        }

        $subjectsQuery->delete();

        flash()->option('position', 'bottom-right')->success('Selected subjects deleted successfully.');

        return back();
    }




    public function edit(Request $request, $id)
    {
        // Get the currently authenticated user's school ID
        $subject = Subject::findOrFail($id);
       

        return view('subjects.create',['subject'=>$subject]);
    }
    

    /**
     * Update the specified subject in storage.
     */
    public function update(Request $request, $id)
    {

        $schoolId = auth()->user()->school_id;
    
        // Validate the request data
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Ensure the name is unique within the same school, except for the current subject
                Rule::unique('subjects')->where(function ($query) use ($schoolId, $id) {
                    return $query->where('school_id', $schoolId)
                                 ->where('id', '!=', $id); // Exclude current subject
                }),
            ],
        ]);
    
        // Find the subject by ID and update
        $subject = Subject::findOrFail($id);
        $subject->update($request->all());
    
        flash()->option('position', 'bottom-right')->success('Subject updated successfully.');

        // Redirect back to the subjects index with a success message
        return redirect()->route('subjects.index');
      

       
    }

    /**
     * Remove the specified subject from storage.
     */
    public function destroy($id)
    {
        $subject = Subject::findOrFail($id);
        $subject->delete();
        flash()->option('position', 'bottom-right')->success('Subject deleted successfully.');

        return redirect()->route('subjects.index');
    }

    /**
     * Display a listing of subjects.
     */
// public function index(Request $request)
// {
//     $schoolId = auth()->user()->school_id;
//     $school = School::with(['combinations'])->find($schoolId);
//     $levels = Arr::wrap($school->school_type);


//     $subjects = $school->subjects()->get();
//     if (!empty($levels)) {
//     $subjects = Subject::whereJsonContains('school_level', $level)->get();
//             }
        


//     $combinations = Combination::whereDoesntHave('schools', function ($query) use ($schoolId) {
//         $query->where('school_id', $schoolId);
//     })->get();

//     return view('subjects.list', compact('subjects', 'school', 'combinations'));
// }

public function index(Request $request)
{
    $school = Auth::user()->school;

    if (! $school) {
        return back()->with('error', 'School not found.');
    }

    // 1. Retrieve array of assigned combination IDs for the school
    $schoolCombinationIds = is_array($school->combinations) 
        ? $school->combinations 
        : json_decode($school->combinations ?? '[]', true);

    $levels = Arr::wrap($school->school_type);

    // -------------------------------------------------------------
    // 2. Fetch Direct School Subjects & Level Subjects ($allSubjects)
    // -------------------------------------------------------------
    $directSchoolSubjects = method_exists($school, 'subjects') ? $school->subjects()->get() : collect();
    $schoolSubjectIds = $directSchoolSubjects->pluck('id')->toArray();

    $levelSubjects = Subject::query()
        ->when(! empty($levels), function ($query) use ($levels) {
            $query->where(function ($subQuery) use ($levels) {
                foreach ($levels as $level) {
                    $subQuery->orWhereJsonContains('school_level', $level);
                }
            });
        })
        ->get();

    // Master list of all available subjects for the school
    $allSubjects = $directSchoolSubjects
        ->merge($levelSubjects)
        ->unique('id')
        ->sortBy('name')
        ->values();

    // -------------------------------------------------------------
    // 3. Fetch Assigned Combinations & Pivot Records
    // -------------------------------------------------------------
    $combinations = Combination::whereIn('id', $schoolCombinationIds)->get();

    $assignedPivotRecords = DB::table('combination_subject')
        ->whereIn('combination_id', $combinations->pluck('id'))
        ->get()
        ->keyBy('combination_id');

    // Extract predefined pivot subject IDs
    $predefinedSubjectIds = [];
    foreach ($assignedPivotRecords as $rec) {
        $ids = is_array($rec->subject_id) ? $rec->subject_id : json_decode($rec->subject_id ?? '[]', true);
        if (is_array($ids)) {
            $predefinedSubjectIds = array_merge($predefinedSubjectIds, $ids);
        }
    }
    $predefinedSubjectIds = array_unique(array_map('intval', $predefinedSubjectIds));

    // Extract extra school-specific subject IDs linked via Subject's combination_id JSON
    $extraSchoolSubjectIds = $allSubjects->filter(function ($subject) use ($schoolCombinationIds) {
        $combIds = is_array($subject->combination_id) 
            ? $subject->combination_id 
            : json_decode($subject->combination_id ?? '[]', true);

        return is_array($combIds) && count(array_intersect($schoolCombinationIds, $combIds)) > 0;
    })->pluck('id')->toArray();

    // Combined list of ALL combination subject IDs (Predefined + Extra Added)
    $allCombinationSubjectIds = array_unique(array_merge($predefinedSubjectIds, $extraSchoolSubjectIds));

    // -------------------------------------------------------------
    // 4. Build Filtered $subjects for Display Grid
    // -------------------------------------------------------------
    // Includes direct school subjects AND all subjects attached to active combinations
    $subjects = $allSubjects->filter(function ($subject) use ($allCombinationSubjectIds, $schoolSubjectIds) {
        return in_array($subject->id, $allCombinationSubjectIds, true) 
            || in_array($subject->id, $schoolSubjectIds, true);
    })->sortBy('name')->values();

    // -------------------------------------------------------------
    // 5. Fetch Unassigned Combinations for Modal Dropdown
    // -------------------------------------------------------------
    $unassignedCombinations = Combination::whereIn('level', $levels)
        ->whereNotIn('id', $schoolCombinationIds)
        ->get();

    $unassignedPivotRecords = DB::table('combination_subject')
        ->whereIn('combination_id', $unassignedCombinations->pluck('id'))
        ->get()
        ->keyBy('combination_id');

    $predefinedSubjectsMap = [];
    foreach ($unassignedCombinations as $comb) {
        $pivot = $unassignedPivotRecords->get($comb->id);
        $rawIds = $pivot ? (is_array($pivot->subject_id) ? $pivot->subject_id : json_decode($pivot->subject_id ?? '[]', true)) : [];
        $predefinedSubjectsMap[$comb->id] = array_map('intval', $rawIds ?? []);
    }

    // -------------------------------------------------------------
    // 6. Package Assigned Combinations (Predefined + Extra Added)
    // -------------------------------------------------------------
    $subjectNamesMap = $allSubjects->pluck('name', 'id');

    $packagedCombinations = [];
// Step 0: Pre-fetch combination_extras records for the school to avoid N+1 queries in the loop
$extrasMap = DB::table('combination_extras')
    ->where('school_id', $user->school_id)
    ->pluck('subject_id', 'combination_id');

foreach ($combinations as $combination) {
    // Source A: Predefined subject names from pivot table
    $pivot = $assignedPivotRecords->get($combination->id);
    $pivotSubjectIds = $pivot ? (is_array($pivot->subject_id) ? $pivot->subject_id : json_decode($pivot->subject_id ?? '[]', true)) : [];

    $pivotSubjectNames = collect($pivotSubjectIds)
        ->map(fn ($id) => $subjectNamesMap->get($id))
        ->filter()
        ->toArray();

    // Source B: Added school-specific subject names from Subject's combination_id JSON
    $extraSchoolSubjectNames = $allSubjects->filter(function ($subject) use ($combination) {
        $combIds = is_array($subject->combination_id) 
            ? $subject->combination_id 
            : json_decode($subject->combination_id ?? '[]', true);

        return is_array($combIds) && in_array($combination->id, $combIds);
    })->pluck('name')->toArray();

    // Source C: Extra general subject names from combination_extras table
    $extraGeneralRaw = $extrasMap->get($combination->id);
    $extraGeneralIds = is_array($extraGeneralRaw) 
        ? $extraGeneralRaw 
        : json_decode($extraGeneralRaw ?? '[]', true);

    $extraGeneralSubjectNames = collect($extraGeneralIds)
        ->map(fn ($id) => $subjectNamesMap->get($id))
        ->filter()
        ->toArray();

    // Merge all three sources, remove duplicates, and sort alphabetically
    $mergedSubjectNames = array_values(array_unique(array_merge(
        $pivotSubjectNames, 
        $extraSchoolSubjectNames, 
        $extraGeneralSubjectNames
    )));
    
    natcasesort($mergedSubjectNames);

    $packagedCombinations[] = [
        'id' => $combination->id,
        'name' => $combination->name,
        'subjects' => array_values($mergedSubjectNames),
    ];
}

    return view('subjects.list', compact(
        'subjects',                // Display Grid (Active combination subjects + Direct school subjects)
        'allSubjects',             // Modal Dropdown (All level subjects + Direct school subjects)
        'school',
        'combinations',
        'unassignedCombinations',
        'packagedCombinations',    // Combination Cards (Contains predefined + added extra subjects)
        'predefinedSubjectsMap',
        'schoolSubjectIds'
    ));
}
    
    /**
     * Display the specified subject.
     */
    public function show($id)
    { 
        $subject = Subject::findOrFail($id);


        // dd($subject->streams);
        return view('subjects.view',compact('subject'));
    }
}
