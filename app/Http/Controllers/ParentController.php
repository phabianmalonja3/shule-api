<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Payment;
use App\Models\Student;
use App\Models\ParentModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\StreamSubjectTeacher;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class ParentController extends Controller implements HasMiddleware
{
    /**
     * Display a listing of the resource.
     */


     public static function middleware(): array
    {
        return [
            // new Middleware('checkSubscriptionStatus', except: ['store','create']),
            // 'auth',
        ];
    }
    public function index()
    {

        $user = auth()->user(); 
        // $students = $user->parent->students; 

        $students = $user->parent->students;
        $school= $students->first()->school;

        // dd($school);
        $annoucements = $school->annoucements()->paginate(3);



        $student = Student::with('stream')->findOrFail($students->first()->id);



    
    // Retrieve teachers who belong to this student's stream
    $teachers = StreamSubjectTeacher::with(['teacher', 'subject'])
    ->where('stream_id', $student->stream->id)
    ->get();
  

        // dd($annoucements);


        return view('parents.dashboard', compact('students','school','annoucements','teachers'));

    
    }

    /**login
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // dd('ok');
        return view('parents.create');
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {          return $request;
        try {
            DB::beginTransaction();

            $request->validate([
                'student_registration_number' => ['required','regex:/^\d{4}\/\d{2}\/\d{4}$/'],
                'phone' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^0[6-7][0-9]{8}$/'], 
                'password' => ['required', 'string', 'min:8', 'confirmed'],
                'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users'],
                'gender' => 'required|in:male,female',
                'relationship' => 'required|in:parent,aunt,uncle,sibling,grandmother,grandfather,sponsor,others',
                'first_name' => 'required|string|max:255',
                'middle_name' => 'nullable|string|max:255',
                'surname' => 'required|string|max:255',                
            ]);

            $student = Student::where('reg_number', $request->student_registration_number)->first();

            if (!$student) {
                flash()->option('position','bottom-right')->error('Student with this registration number does not exist.');
                
                DB::rollBack(); 
                return redirect()->back();         
            }

            if ($student->parents()->count() >= 4) { // Check the actual number of associated parents
                flash()->option('position','bottom-right')->warning('A student cannot have more than 4 parents/guardians. Please contact the school.');
                DB::rollBack();
                return redirect()->back();
            }

            $user = User::create([
                    'name' => $request->first_name . ' ' . $request->middle_name. ' ' . $request->surname,
                    'phone' => $request->phone,
                    'username' => $request->phone,
                    'password' => 'password',
                    'email' => $request->email,
                    'gender' => $request->gender,
                    
            ]);

            $parent = ParentModel::create([
                    'user_id'=>$user->id,
                    'first_name'=>$request->first_name,
                    'middle_name'=>$request->middle_name,
                    'sur_name'=>$request->surname,
                    'phone'=>$request->phone,
                    'relationship' => $request->relationship,
                    'gender' => $request->gender,
                ]);

            $parent->students()->attach($student->id);

            DB::commit();

            // Optionally, you can log the parent in after successful registration
            $user->assignRole('parent');
            flash()->option('position','bottom-right')->success('Parent account created successfully!');

            return redirect()->route('login'); 

        } catch (\Exception $e) {
            DB::rollback(); 
            Log::error($e); 
            flash()->option('position','bottom-right')->error('An error occurred while registering. Please try again later.'.$e->getMessage());

            return redirect()->back()->withErrors(['error' => 'An error occurred while registering. Please try again later.'.$e->getMessage()]);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    public function payment()
    {
    return view('payments.parent-payment');
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


    public function viewSubscriptions()
{
    $subscriptions = Payment::where('parent_id', auth()->user()->parent->id) // Get subscriptions for the logged-in parent
                            ->orderByDesc('subscription_end') // Order by subscription end date, descending
                            ->get();

    return view('subscriptions.index', compact('subscriptions'));
}

}
