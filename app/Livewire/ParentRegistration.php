<?php

namespace App\Livewire;

use App\Models\ParentModel;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class ParentRegistration extends Component
{
    public $studentRegistrationNumber;
    public $studentDetails;
    public
        $first_name,
        $middle_name,
        $surname,
        $phone,
        $password,
        $password_confirmation,
        $gender,
        $relationship,
        $email;

    public $step = 1; // Control the current form step

    protected $rules =
    [
        'studentRegistrationNumber' => 'required|string',
        'first_name' => 'required|string|max:255',
        'surname' => 'required|string|max:255',
        'phone' => ['required', 'string', 'max:255', 'unique:users', 'regex:/^0[6-7][0-9]{8}$/'],
        'password' => 'required|min:8|confirmed',
        'gender' => 'required|in:male,female',
        'relationship' => 'required|in:parent,aunt,uncle,sibling,grandmother,grandfather,sponsor,others',
        'email' => 'nullable|email',
    ];
    public function fetchStudentDetails()
    {
        $this->validate(['studentRegistrationNumber' => 'required|string']);

        $student = Student::where('reg_number', $this->studentRegistrationNumber)->first();

        if ($student) {
            $this->studentDetails = $student;
            $this->step = 2; // Move to the registration form step
        } else {
            $this->addError('studentRegistrationNumber', 'Student with this registration number does not exist.');
        }
    }

    public function registerParent()
    {

        $this->validate();

        try {
            DB::beginTransaction();

            $student =  Student::where('reg_number', $this->studentRegistrationNumber)->first();

            if ($student->parents()->count() >= 4) { // Check the actual number of associated parents
                flash()->option('position','bottom-right')->warning('A student cannot have more than 4 parents/guardians. Please contact the school.');
                DB::rollBack();
                return redirect()->back();
            }
            
            $user = User::create([
                'name' => $this->first_name,
                'phone' => $this->phone,
                'username' => $this->phone,
                'password' => 'password',
                'email' => $this->email,
                'gender' => $this->gender,

            ]);
            $parent = ParentModel::create([
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'middle_name' => $this->middle_name,
                'sur_name' => $this->surname,
                'relationship' => $this->relationship,
                'phone' => $this->phone,
                'gender' => $this->gender,
            ]);

            // $student->parent_id = $parent->id;
            // $student->save();
//             dd($student);
// dd($student->id.'-'.$parent->id);
            $parent->students()->attach($student->id);
            
            DB::commit();
            $user->assignRole('parent');
            flash()->option('position', 'bottom-right')->success('Parent account created successfully.');

            Auth::login($user);
            return redirect()->route('parents.index');
            // return redirect()->route('login');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            flash()->option('position', 'bottom-right')->error('An error occurred while registering. Please try again later.' . $e->getMessage());

            return redirect()->back()->withErrors(['error' => 'An error occurred while registering. Please try again later.' . $e->getMessage()]);
        }
    }

    public function render()
    {
        return view('livewire.parent-registration');
    }
}
