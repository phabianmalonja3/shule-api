<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    
     public function view(Request $request){
        return view("auth.login");
     }
    public function store(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string'], 
            'password' => 'required|string|min:6',
        ]);
    
        if (Auth::attempt(['username' => $request->phone, 'password' => $request->password, 'is_verified' => true])) {
            $request->session()->regenerate();
    
            $user = Auth::user();
    dd($user);
            // Check school activation status
            if (!$user->school || $user->school->is_active) {
                $role = $user->roles->first(); // Get the first role (assuming only one role per user)
                
                if ($role) {
                    $routeName = match ($role->name) {
                        'administrator' => 'admin.home',
                        'header teacher' => 'welcome.head.teacher',
                        'academic teacher' => 'welcome.head.teacher', // Combined with 'header teacher' route
                        'class teacher' => 'teacher.panel',
                        'teacher' => 'teacher.panel',
                        'student'=>'student.panel',
                        'parent'=>'parents.index',
                        default => null,
                    };

                    flash()->option('position', 'bottom-right')->success('Login successfully.');
                    return redirect()->route($routeName);
                }
            } else {
                Auth::logout();
                flash()->option('position', 'bottom-right')->error('Your school account is deactivated.');
                return redirect()->route('login');
            }
        }
    
        flash()->option('position', 'bottom-right')->error('The provided credentials do not match our records.');
        return back();
    }
    


}
