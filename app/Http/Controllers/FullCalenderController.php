<?php
 
  namespace App\Http\Controllers;
  
  use Illuminate\Http\Request;
  use App\Models\Event;
  use Illuminate\Http\JsonResponse;
  
  class FullCalenderController extends Controller
  {
      /**
       * Handle full calendar events request.
       *
       * @return JsonResponse
       */
      public function index(Request $request)
      {
          if ($request->ajax()) {
              $data = Event::where('start', '>=', $request->start)
                           ->where('end', '<=', $request->end)
                           ->get(['id', 'title', 'start', 'end', 'allDay']); // Include allDay
      
              return response()->json($data);
          }
      
          return view('calander.index');
      }
      /**
       * Handle AJAX requests for adding, updating, or deleting events.
       *
       * @return JsonResponse
       */
      public function ajax(Request $request)
{
    switch ($request->type) {
        case 'add':
            $event = Event::create([
                'title' => $request->title,
                'start' => $request->start,
                'end' => $request->end,
                'allDay' => $request->allDay ?? false, // Handle allDay
            ]);
            return response()->json($event);
            break;

        case 'update':
            $event = Event::find($request->id)->update([
                'title' => $request->title,
                'start' => $request->start,
                'end' => $request->end,
                'allDay' => $request->allDay ?? false, // Handle allDay
            ]);
            return response()->json($event);
            break;

        case 'delete':
            $event = Event::find($request->id)->delete();
            return response()->json($event);
            break;

        default:
            return response()->json(['error' => 'Invalid request type'], 400);
            break;
    }
}
  }