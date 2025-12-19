<?php

namespace App\Http\Controllers\Api\V1;

use Storage;
use App\Models\Announcement;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\AnnouncementCollection;
use Illuminate\Support\Facades\Storage as FacadesStorage;

class AnnouncementController extends Controller
{


    public function parentAnnouncements()
    {

        return view('parents.announcement.index');
    }



public function create()
{

    $announcement = null;
    return view('annoucement.create',compact('announcement'));
}
// public function index(Request $request)
// {
//     // $userSchoolId = auth()->user()->school_id;
//     // $searchTerm = $request->get('search');

//     // $announcementsQuery = Announcement::where('school_id', $userSchoolId)->latest();

//     // if ($searchTerm) {
//     //     $announcementsQuery->where(function ($query) use ($searchTerm) {
//     //         $query->where('title', 'like', "%$searchTerm%")
//     //               ->orWhere('content', 'like', "%$searchTerm%");
//     //     });
//     // }

//     // $announcements = $announcementsQuery->paginate(10);

//     // if ($request->ajax()) {
//     //     return response()->json([
//     //         'table' => view('annoucement.partials.announcement_rows', ['announcements' => $announcements])->render(),
//     //         'pagination' => $announcements->links()->render(),
//     //     ]);
//     // }

//     $user = auth()->user();
// $userSchoolId = $user->school_id;
// $searchTerm = $request->get('search');

// $announcementsQuery = Announcement::where('school_id', $userSchoolId)->latest();

// // Role-based filtering
// if ($user->hasRole('headmaster')) {
//     // Headmaster should see all announcements
// } elseif ($user->hasRole('academic teacher')) {
//     // Academic teacher should see their own announcements + headmaster’s announcements
//     $announcementsQuery->where(function ($query) use ($user) {
//         $query->where('user_id', $user->id)
//               ->orWhereHas('user', function ($subQuery) {
//                   $subQuery->whereHas('roles', function ($roleQuery) {
//                       $roleQuery->where('name', 'header teacher');
//                   });
//               });
//     });
// } elseif ($user->hasAnyRole(['teacher', 'class teacher'])) {
//     // Teachers and class teachers should only see internal announcements
//     $announcementsQuery->where('type', 'internal');
// }

// if ($searchTerm) {
//     $announcementsQuery->where(function ($query) use ($searchTerm) {
//         $query->where('title', 'like', "%$searchTerm%")
//               ->orWhere('content', 'like', "%$searchTerm%");
//     });
// }

// $announcements = $announcementsQuery->paginate(10);

// if ($request->ajax()) {
//     return response()->json([
//         'table' => view('annoucement.partials.announcement_rows', ['announcements' => $announcements])->render(),
//         'pagination' => $announcements->links()->render(),
//     ]);
// }


//     return view('annoucement.annoucement-list', compact('announcements'));
// }

    public function index(Request $request) {
        $user = auth()->user();
        $userSchoolId = $user->school_id;
        $searchTerm = $request->get('search');

        // Filter only active announcements first
        $announcementsQuery = Announcement::where('school_id', $userSchoolId)
            // ->where('status',true ) // Ensure only active announcements
            ->latest(); // Ensure latest first

        // Role-based filtering
        if ($user->hasRole('header teacher')) {
            $announcementsQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                    ->orWhereHas('user.roles', function ($subQuery) {
                        $subQuery->whereIn('name', ['assistant headteacher','academic teacher']);
                    });
            });
        } elseif ($user->hasRole('academic teacher')) {
            // Academic teacher sees their own + headmaster's announcements
            $announcementsQuery->where(function ($query){
                $query->WhereHas('user.roles', function ($subQuery) {
                        $subQuery->whereIn('name', ['header teacher','assistant headteacher','academic teacher']);
                    });
            });
        } elseif ($user->hasRole('teacher')) {
            $announcementsQuery->where(function ($query) use ($user) {
                $query->where('type', 'internal')
                    ->orWhere('type', 'both');
            });
        }

        // Apply search filtering
        if ($searchTerm) {
            $announcementsQuery->where(function ($query) use ($searchTerm) {
                $query->where('title', 'like', "%$searchTerm%")
                    ->orWhere('content', 'like', "%$searchTerm%");
            });
        }

        // Pagination
        $announcements = $announcementsQuery->paginate(10);

        // Handle AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'table' => view('annoucement.partials.announcement_rows', ['announcements' => $announcements])->render(),
                'pagination' => $announcements->links()->render(),
            ]);
        }

        return view('annoucement.annoucement-list', compact('announcements'));
    }

    public function toggleStatus(Request $request)
    {
        $announcement = Announcement::findOrFail($request->announcementId);
        $announcement->status = $request->status;
        $announcement->save();


        return response()->json([
            'success' => true,
            'message' => $request->status == 1 ?  'Announcement published.' : 'Announcement unpublished.',
        ]);
    }

    public function edit(Announcement $announcement)
    {
        // Pass the specific announcement to the view
        return view('annoucement.create', compact('announcement'));
    }

    public function show($id)
    {
        // Get the authenticated user's school ID
        $schoolId = Auth::user()->school_id;

        // Find the announcement by ID and ensure it belongs to the user's school
        $announcement = Announcement::where('id', $id)->where('school_id', $schoolId)->first();
        if(is_null($announcement)) {
            abort(404);
        }

            return view('annoucement.show', compact('announcement'));
    }

    public function update(Request $request, Announcement $announcement)
    {
        // Check if the user has the 'update announcement' permission


        // Validate the request data
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'content' => 'sometimes|string',
        ]);

        // Update the announcement with the validated data
        $announcement->update($validated);

        flash()->option('position','bottom-right')->success('your annoucement Updated');
        return back();
    }

    public function destroy($id)
    {
        // Check if the user has the 'delete announcement' permission



        // Find the announcement by its ID
        $announcement = Announcement::findOrFail($id);
        if ($announcement->image) {
            FacadesStorage::delete('public/' . $announcement->image);
        }
        // Delete the announcement
        $announcement->delete();
        flash()->option('position','bottom-right')->success('succesfull deleted');

    return back();
    }

}
