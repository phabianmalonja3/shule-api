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
		$schoolId = Auth::user()->school_id;
        $request->validate([
			'combination_id' => [
				'required',
				'exists:combinations,id',
				Rule::unique('combination_school', 'combination_id')
					->where('school_id', $schoolId)
            ],
            
            'subjects' => 'required|array|min:1',
            'subjects.*' => 'required|integer|exists:subjects,id',
        ]);
		
		$subjectsInput = $request->input('subjects');

        $cleanSubjectIds = array_map(function($id) {
            return (int) trim($id); 
        }, $subjectsInput);
        
        $request->merge(['subjects' => $cleanSubjectIds]);
    
		$school = School::find($schoolId);

		$school->combinations()->attach($request->input('combination_id'), [
            'created_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

		$combination = Combination::find($request->input('combination_id'));
		$generalSubjects    = ['English Language','Business Studies','Historia ya Tanzania na Maadili','Kiswahili','Basic Mathematics','Geography'];
		$generalSubjectIDs = $school->subjects()->whereIn('name',$generalSubjects)->pluck('id')->toArray();
        $subjectsToSync = [];
		$combinedSubjects = array_merge($generalSubjectIDs, $request->input('subjects'));

        $combination->subjects()->sync($combinedSubjects);
		
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
			'combination_id' => 'required|exists:combinations,id'
		]);
		
		$id = $request->combination_id;
		$combination = Combination::findOrFail($id);
		if ($combination->students()->exists()) {
			return back()->with('error', 'Cannot delete because students are enrolled in this combination.');
		}
		$schoolId = Auth::user()->school_id;
		$school = School::find($schoolId);
		$combination->subjects()->detach();

		$school->combinations()->detach($id);

        flash()->option('position', 'bottom-right')->success('Combination deleted successfully.');

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
    public function index(Request $request)
    {
        $schoolId = auth()->user()->school_id;

        //$classes = SchoolClass::where('school_id', $schoolId)->get();
        //$classId = $request->input('class_id') ?? $classes->first()->id;
		$school = School::with(['combinations'])->find($schoolId);
		$generalSubjects    = ['English Language','Business Studies','Historia ya Tanzania na Maadili','Kiswahili','Basic Mathematics','Geography'];
        $subjects = $school->subjects()->whereNotIn('name',$generalSubjects)->orderBy('name')->paginate(20);
		$combinations = Combination::whereDoesntHave('schools', function ($query) use ($schoolId) {
				$query->where('school_id', $schoolId);
			})->get();

// MISPLACED -- Used for entering subjects in bulky		
/*		$allSubjects = ['Kiswahili']; // All levels
		$primaryALevelSubjects = ['Mathematics','English']; // Only primary and A-level
		$primarySubjects = ['Science and Technology','Social Studies','Civic and Moral Education','Religious Education','Vocational Skills']; //Only primary schools
		$allsecondarySubjects = ['Kiswahili Literature', 'English Language', 'Literature in English','Physics', 'Chemistry', 'Biology', 'History', 'Geography', 'Historia ya Tanzania na Maadili','Business Studies','Bible Knowledge','Islamic Knowledge','Arabic','French','Chinese','Agriculture','Computer Science','Fine Arts','Music','Textile and Garment Construction','Sports','Food and Nutrition']; // Both O-level and A-level
		$extra = ['Food and Nutrition'];
		$olevelSubjects = ['Basic Mathematics', 'Additional Mathematics','Book-Keeping','Performing Arts'];
		$alevelSubjects = ['Academic Communications','Accountancy','Economics','Basic Applied Mathematics','Divinity','Theatre Arts',]; 

		foreach ($extra as $subject) {
			$school->subjects()->create(['name' => $subject, 'created_by_system' => true, 'school_level' => json_encode(['O-Level','A-Level'])]);
		} 
*/		

        //return view('subjects.list', compact('subjects', 'classes', 'classId'));
        return view('subjects.list', compact('subjects', 'school', 'combinations'));
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
