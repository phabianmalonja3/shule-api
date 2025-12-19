<?php

namespace App\Livewire\Announcements;

use App\Models\User;
use Livewire\Component;
use App\Models\Assignment;
use App\Models\Announcement;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AnnouncementCreate extends Component
{

    use WithFileUploads;


    public $title,$image,$start_date,$end_date,$due_date,$content,$type,$announcement;



    protected $rules = [
        'title' => 'required|string|max:255',
        'content' => 'required|string',
        'image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        'start_date' => 'required|date|after_or_equal:today', // Ensure start date is today or later
        'end_date' => 'required|date|after_or_equal:start_date',
        'type' => 'required|string'
    ];


    public function mount($announcement =null){
        if($announcement){
            $this->announcement =$announcement;
            $this->title =$announcement->title;
            $this->content =$announcement->content;
            $this->image =$announcement->file_path;
            $this->type =$announcement->type;
            $this->start_date = optional($announcement->start_date)->format('Y-m-d');
            $this->end_date = optional($announcement->end_date)->format('Y-m-d');

        }
       
                    if (Auth::user()->hasRole('header teacher') || Auth::user()->hasRole('academic teacher')) {
                $this->type = null;
            }elseif(Auth::user()->hasRole('class teacher')){
                $this->type = 'external';
            }else{
                 $this->type = 'external';
            }
    }

    public function render()
    {
        

        return view('livewire.announcements.announcement-create');
    }

    
    public function resetInputFields()
    {
        $this->announcement = null;
        $this->title = null;
        $this->content = null;
        $this->image = null;
        $this->type = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->type = null;
    }

    public function saveAnnouncement()
    {
        // Validate the incoming request
        $this->validate();
        

        $user = auth()->user();
        if($user->hasRole('class teacher')){
         $this->type= 'external';
        }

        // Get the authenticated user

        if ($this->image) {
            // Store the image in the 'public/announcements' directory
            $imagePath = $this->image->store('announcements','public');
        } else {
            $imagePath = null; // If no image is uploaded, set it to null
        }


        $schoolId = $user->school_id;


        // dd($request->is_active);
        try {

            DB::beginTransaction();
            // Create the announcement
            Announcement::create([
                'title' => $this->title,
                'content' => $this->content,
                'school_id' => $schoolId,
                'image' => $imagePath, // Save the image path in the database
                'user_id' => $user->id, // Save the image path in the database  // Default status
                'start_date' => $this->start_date, // Save the image path in the database  // Default status
                'end_date' => $this->end_date,
                'type'=>$this->type,
                'is_active'=>true // Save the image path in the database  // Default status
            ]);

         DB::commit();
            flash()->option('position','bottom-right')->success('Announcement created successfully');
            // Return a successful response
            $this->resetInputFields();
            return redirect()->route('announcements.index');
        } catch (\Exception $e) {
            // Handle any errors that occur
            DB::commit();
            flash()->option('position','bottom-right')->error('There wa an error Due :'.$e->getMessage());
            return back();// Internal Server Error
        }
    }

    public function editAnnouncement($id)
{
    

    try {
        // Handle image upload if a new image is provided
        if ($this->image) {
            // Delete the old image if it exists
            if ($this->announcement->file_path) {
                \Storage::delete($this->announcement->file_path);
            }

            // Store the new image
            $file_path = $this->image->store('public/announcements');
            $this->announcement->file_path = $file_path;
        }
      

        // Update the announcement details
        $this->announcement->update([
            'title' => $this->title,
            'content' => $this->content,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'type' => $this->type,
            'is_active' => $this->is_active ?? $this->announcement->is_active, // Default to current status if not provided
        ]);

    
        flash()->option('position', 'bottom-right')->success('Announcement updated successfully.');
        return redirect()->route('announcements.index');
    } catch (\Exception $e) {
        // Handle any errors during the update
        flash()->option('position', 'bottom-right')->error('Error updating announcement: ' . $e->getMessage());
        return back();
    }
}


    public function sendToParents($announcement)
    {
        if (auth()->user()->hasRole('Class Teacher')) {
            $class = auth()->user()->class;
    
            // Fetch parents associated with the teacher's class
            $parents = User::whereHas('students', function ($query) use ($class) {
                $query->where('class_id', $class->id);
            })->get();
    
            // Send notifications to parents
            // Notification::send($parents, new AnnouncementNotification($announcement));
        }
    }
    
}
