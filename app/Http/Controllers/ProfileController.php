<?php



namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // Show the profile page
    public function show()
    {
        return view(view: 'profile.profile');
    }

    // Update profile information


    // Change user password
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ]);

        $user = auth()->user();

        // Check if the current password matches
        if (!Hash::check($request->current_password, $user->password)) {
            flash()->option('position','bottom-right')->error('Incorrect current password.');
            return back();
        }

        if ($request->current_password == $request->new_password) {
            flash()->option('position','bottom-right')->error('Please use a different password.');
            return back();
        }
        // Update the password
        $user->password = Hash::make($request->new_password);
        $user->save();


        flash()->option('position','bottom-right')->success('Password changed successfully.');

        return redirect()->back();
    }
    
public function updateProfilePicture(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'gender' => 'required|string|max:6',
        'email' => 'nullable|email|unique:users,phone,' . auth()->id(),
        'profile_picture' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
    ]);

    $user = auth()->user();

    // Handle profile picture upload
    if ($request->hasFile('profile_picture')) {
        // Delete old profile picture if it exists
        if ($user->profile_picture) {
            Storage::delete($user->profile_picture);
        }

        // Store the new profile picture
        $path = $request->file('profile_picture')->store('profile_pictures');
        $user->profile_picture = $path;
    }

    // Update user details
    $user->gender = $request->gender ?? '';
    $user->email = $request->email ?? '';
    $user->save();
    flash()->option('position','bottom-right')->success('Profile updated successfully.');

    return redirect()->back();
}

public function updateProfilePhone(Request $request)
{
    $request->validate([
       
        'curret_phone' => 'required|string|exists:users,phone',
        'new_phone' => 'required|unique:users,phone,except,id',
        
    ]);


    try{

        DB::beginTransaction();

        $user = User::where('phone',$request->curret_phone)
        ->where('id',auth()->user()->id)
    ->first();




    if(!$user){
        flash()->option('position','bottom-right')->error('Your phone number is not exixting to our system');
        return back();

    }

    $user->phone = $request->new_phone;
    $user->username = $request->new_phone;
    $user->save();

    DB::commit();
    flash()->option('position','bottom-right')->success('Username Updated Successfully.');

    Auth::logout();
 
    $request->session()->invalidate();
 
    $request->session()->regenerateToken();
 
return redirect()->route('login');


    }catch(\Exception $e){
        DB::commit();
        flash()->option('position','bottom-right')->error('Error Failed  to Update.' .$e->getMessage());
        return back();
    }
 
}

}
