<?php

namespace App\Livewire;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FetchStudentDetails extends Component
{



    public $studentNumber;
    public $studentDetails;
    public $buttonDisabled = true;
    public $method;
    public $student_id;  // Now a single student ID
    public $amount;
    public $phone;

    public $students = []; // Holds all students added dynamically
    public $student_ids = []; // Holds all students added dynamically
    public $registrationNumber; // Input for fetching student details // Phone number for the subscription
    public $errorMessage;
    protected $rules = [ 
        'phone' => ['required', 'string', 'max:255', 'regex:/^0[6-7][0-9]{8}$/'], 
    ];


    


    public function fetchStudent()
    {
        $student = Student::where('reg_number', $this->registrationNumber)->first();

        if ($student) {
            // Add the student details to the repeater array
            $this->students[] = [
                'id' => $student->id,
                'registration_number' => $student->registration_number,
                'name' => $student->user->name,
                'school' => $student->school->name,
            ];
            $this->buttonDisabled = false;
            $this->registrationNumber = ''; // Clear the input field
            $this->errorMessage = null; // Clear any error messages
        } else {
            $this->errorMessage = "Student not found. Please check the registration number.";
        }
    }

    public function removeStudent($index)
    {
        // Remove a specific student from the array
        unset($this->students[$index]);
        $this->students = array_values($this->students); // Re-index the array
    }

    public function addStudent()
    {
        $this->students[] = [
            'school' => '',
            'name' => '',
        ];
    }
    
    // Method to remove a student
   


    public function render()
    {


        return view('livewire.fetch-student-details');
    }


    public function updatedStudentId()
    {
        // Recalculate the amount when a student is selected
        $this->amount = 10000.00;  // Assuming the amount is fixed for a single student
    }

    public function subscribe()
    {
        $this->validate();

        // dd($this->phone);
        
        DB::beginTransaction();


        foreach($this->students as $student){

           $this->student_ids[] = $student['id'];
        }
     

        try {
            // Create the payment record for a single student
            $payment = Payment::create([
                'parent_id' => auth()->user()->parent->id,
                'amount' => 10000,
                'method' => 'cash',
                'status' => 'Pending',
                'transaction_id' => null,
                'subscription_start' => now(),
                'subscription_end' => now()->addYear(),
            ]);

            $payment->students()->attach( $this->student_ids);

            // Payment gateway logic (mock or real)
            $paymentStatus = 'Paid';

            if ($paymentStatus === 'Paid') {
                $payment->update([
                    'status' => 'Paid',
                    'student_id' =>  $this->student_ids,
                    'transaction_id' => 'TXN-' . strtoupper(uniqid()),
                ]);

                DB::commit();

                
            flash()->option('position','bottom-right')->success('Subscription successful!');

                return $this->redirectRoute('parents.index');
            }

            // If payment failed, rollback and return error
            DB::rollBack();
            session()->flash('error', 'Payment failed.');
            flash()->option('position','bottom-right')->error('Payment failed');

            return back();
        } catch (\Exception $e) {
            DB::rollBack();
            // session()->flash('error', 'An error occurred: ' . $e->getMessage());
            flash()->option('position','bottom-right')->error('Payment failed ' .$e->getMessage());

            return back();
        }
    }

}
