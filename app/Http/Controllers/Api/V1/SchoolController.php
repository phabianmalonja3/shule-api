<?php

namespace App\Http\Controllers\Api\V1;


use App\Models\User;
use App\Models\School;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SchoolCollection;
use App\Http\Resources\School as SchoolResource;
use App\Http\Resources\School as ResourcesSchool;

class SchoolController extends Controller


{

    public function index(Request $request)
    {
        // Initialize the query
        $query = School::query();
        // Apply filtering if the 'name' parameter is provided

            $query->where('name', 'LIKE', '%' . $request->search . '%')
            ->orWhere('registration_number','LIKE','%'.$request->search.'%')
            ->orWhere('address', 'LIKE', '%' . $request->search . '%')
            ->orWhere('is_active',  $request->search);


        // Paginate the results (e.g., 4 per page)
        $schools = $query->latest()->paginate(6);

        // Return paginated and filtered results as a resource collection
        return  view('admin.school-list',compact('schools'));
    }

    public function addTeacherToSchool(Request $request, $schoolId)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',

        ]);




        // Check if the school belongs to the authenticated Head Teacher
        $school = School::where('id', $schoolId)
            ->where('head_teacher_id', auth()->user()->id)
            ->firstOrFail();
            $defaultPassword = Str::random(8);
        // Create a new teacher
        $teacher = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => $defaultPassword,
        ]);

        // Assign the 'teacher' role
        $teacher->assignRole('teacher');

        // Attach the teacher to the school
        $school->teachers()->attach($teacher->id);

        return response()->json(['status' => true, 'message' => 'Teacher added to school successfully', 'teacher' => $teacher], 201);
    }



    public function register(SchoolRequest $request)
    {
        // Get the authenticated user (the head teacher)
        $headTeacher = Auth::user();

        // Handle logo upload
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public'); // Store the logo in the public disk
        }

        // Create the school
        $school = School::create([
            'name' => $request->name,
            'address' => $request->address,
            'postal_code' => $request->postal_code,
            'phone_number' => $request->phone_number,
            'logo' => $logoPath, // Save the path of the logo
            'head_teacher_id' => $headTeacher->id, // Link the school to the head teacher
        ]);

        return response()->json([
            'message' => 'School registered successfully.',
            'school' => $school,
        ], 201);
    }
    public function update(Request $request, $id)
    {
        // Validate incoming request
$request->validate([
    'name' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:10',
            'phone_number' => 'nullable|string|max:15',
]);

        // Find the school by ID
        $school = School::find($id);


        // Update the school using mass assignment
        $school->update($request->all());

        return response()->json([
            'message' => 'School updated successfully',
            'school' => $school,
        ]);
    }
    public function destroy($id)
    {
        // Find the school by ID
        $school = School::find($id);
        if (!$school) {
            return response()->json([
                'message' => 'ok',
                'success' => true,
                'data' => 'No data found'

            ], 404);
        } else {
            if ($school->logo) {
                // Use Storage facade to delete the logo if necessary
                Storage::delete($school->logo);
            }

            // Delete the school
            $school->delete();

            return response()->json([
                'message' => 'School deleted successfully',
            ]);
        }
        // Optionally, you might want to handle the logo file deletion if it's stored

    }
    public function show(School $school)
    {
 
        return view('school.school-view',compact('school'));
    }
    public function changeStatus(School $school)
    {

        $school->is_active = !$school->is_active;
        $school->update();

        flash()->option('position','bottom-right')->success('succesfull change the status');
       return back();
    }
}

