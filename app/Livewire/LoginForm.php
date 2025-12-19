<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class LoginForm extends Component
{
    public $phone, $password;
    public $remember = false;

    protected $rules =
     [
        'phone' => 'required|string',
        'password' => 'required|string',
    ];

    public function login()
    {
        $this->validate();
        if (Auth::attempt(['username' => $this->phone, 'password' => $this->password, 'is_verified' => 1])) {
            request()->session()->regenerate();
            $user = Auth::user();
            if (!$user->school || $user->school->is_active) {
                $role = $user->roles->first(); // Get the first role (assuming only one role per user)

                if ($role) {
                    $routeName = match ($role->name) {
                        'administrator' => 'admin.home',
                        'header teacher' => 'welcome.head.teacher',
                        'academic teacher' => 'welcome.head.teacher', // Combined with 'header teacher' route
                        'class teacher' => 'teacher.panel',
                        'teacher' => 'teacher.panel',
                        'student' => 'student.panel',
                        'parent' => 'parents.index',
                        default => null,
                    };

                    flash()->option('position', 'bottom-right')->success('Login successfully');
                    return redirect()->route($routeName);
                }
            } else {
                Auth::logout();
                flash()->option('position', 'bottom-right')->error('Your school account is deactivated.');
                return redirect()->route('login');
            }
        }
      
        flash()->option('position', 'bottom-right')->error('The provided credentials do not match our records or account is inactive.');
        return back();
    }


    public function render()
    {
        return view('livewire.login-form');
    }
}
