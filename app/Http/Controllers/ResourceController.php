<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Subject $subject)
    {
        $resources = $subject->resources()->latest()->paginate(10); // Paginate results

        return view('resources.list', compact('subject', 'resources'));
    }

   
    public function create(Subject $subject)
    {

        $resource = null;
        return view('resources.create', compact('subject','resource'));
    }


    /**
     * Show the form for creating a new resource.
     */
 

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Subject $subject)
    {
        // dd($request->file);
        $request->validate([
            'title' => 'required|string|max:255',
            'resource_type' => 'required|in:notes,past_paper,video,audio,link',
            'file' => [
                // 'required_if:resource_type,notes,past_paper,video,audio',
                'nullable', // Allow null when not required
                'mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mp3',
                 'mimetypes:audio/mpeg,video/mp4',
                'max:10240' // 10MB max size
            ],
            'url' => [
                'required_if:resource_type,link', 
                'nullable', // Allows the field to be empty if not required
                'url', 
                'regex:/^https?:\/\/[\w\-]+(\.[a-zA-Z]{2,6})+([\w\-\.,@?^=%&:\/~+#]*[\w\-\@?^=%&\/~+#])?$/'
            ]
        ]);

        


        try {
            DB::beginTransaction();
    


            $resource = new Resource([
                'subject_id' => $subject->id,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
                'resource_type' => $request->input('resource_type'),
                'created_by' => auth()->id(),
                'url' => $request->input('resource_type') === 'link' ? $request->input('url') : null
            ]);
    
            if ($request->hasFile('file') && in_array($request->resource_type, ['notes', 'past_paper', 'video', 'audio'])) {
                $filePath = $request->file('file')->store('resources', 'public'); // Saves in storage/app/public/resources
                $resource->file_path = $filePath; // Save the path in the database
            }



            
            // dd($filePath);
            $resource->save();
            // dd($resource->file_path);
    
            
            
    
            DB::commit();
    
            flash()->option('position', 'bottom-right')->success('Resource added successfully!');
            return redirect()->route('subjects.resources.index', $subject);
        } catch (\Exception $e) {
            DB::rollback();
            flash()->option('position', 'bottom-right')->error('Error creating Resource! ' . $e->getMessage());
            return back();
        }
    }
    

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit( Subject $subject,Resource $resource)
    {
       return view('resources.create',compact('subject','resource'));
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Subject $subject,  $id)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'resource_type' => 'required|in:notes,past_paper,video,audio,link',
            'file' => [
                'nullable', // Allow no file upload
                'mimes:pdf,doc,docx,jpg,jpeg,png,mp4,mp3',
                'max:10240' // 10MB max size
            ],
            'url' => [
                'required_if:resource_type,link',
                'url',
                'nullable',
                'regex:/^https?:\/\/[\w\-]+(\.[a-zA-Z]{2,6})+([\w\-\.,@?^=%&:\/~+#]*[\w\-\@?^=%&\/~+#])?$/'
            ]
        ]);
        // dd($id);
        $resource = Resource::findOrFail($id);
    

        // dd($resource);

        
$resource->title = $request->title;
$resource->resource_type = $request->resource_type;

if ($request->hasFile('file')) {
    // Delete old file
    if ($resource->file_path) {
        Storage::delete($resource->file_path);
    }
    
    // Store new file
    $path = $request->file('file')->store('resources','public');
    $resource->file_path = $path;
}

if ($request->resource_type === 'link') {
    $resource->url = $request->url;
} else {
    $resource->url = null; // Clear URL if not a link
}

$resource->save();

flash()->option('position', 'bottom-right')->success('Resource Edited successfully!');

return redirect()->route('subjects.resources.index',['subject'=>$subject->id]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject,Resource $resource)
    {

        $resource->delete();

        flash()->option('position', 'bottom-right')->success('Deleted succesfully.');

        return back();
    }
}
