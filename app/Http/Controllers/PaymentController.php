<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class PaymentController extends Controller 
{
    /**
     * Display a listing of the resource.
     */
    // public static function middleware(): array
    // {
    //     return [
    //        'auth',
    //         new Middleware('adminOnly', only: ['index']),
    //     ];
    // }

    public function index(Request $request)
    {

       
        return view('payments.index');
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return view('payments.parent-payment');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        //
    }


public function viewSubscriptions()
{

    $user = auth()->user(); 
    $students = $user->parent->students; 
    $school= $students->first()->school;
    $subscriptions = Payment::with('students')->where('parent_id', auth()->user()->parent->id)->get();

    return view('subscription.index', compact('subscriptions','school'));
}

    public function parentPayments()
{
    $parentId = auth()->id(); // Assuming the parent is logged in

    $payments = Payment::where('parent_id', $parentId)
        ->with('students') // Load related students
        ->orderBy('created_at', 'desc')
        ->get();

    return view('parent.payments', compact('payments'));
}




public function showPaymentForm()
{
   
    // $students = Student::whereHas('parents', function ($query) {
    //     $query->where('parent_id', auth()->user()->parent->id);
    // })->get();


    return view('payments.form');
}

public function showSubscriptionPage()
    {
        return view('subscription.status');
    }

   

}
