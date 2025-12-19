<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

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
            try {
                $data = Event::where('start', '>=', $request->start)
                             ->where('end', '<=', $request->end)
                             ->get(['id', 'title', 'start', 'end', 'allDay']);
                

                return response()->json($data);
            } catch (\Exception $e) {
                
                return response()->json(['error' => 'Error fetching events.'], 500);
            }
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
        try {
            switch ($request->type) {
                case 'add':
                    $event = Event::create([
                        'title' => $request->title,
                        'start' => $request->start,
                        'end' => $request->end,
                        'allDay' => $request->allDay ?? false,
                    ]);
                    Log::info('Event added successfully.', ['event' => $event]);

                    return response()->json($event);
                    break;

                case 'update':
                    $event = Event::find($request->id);
                    if ($event) {
                        $event->update([
                            'title' => $request->title,
                            'start' => $request->start,
                            'end' => $request->end,
                            'allDay' => $request->allDay ?? false,
                        ]);
                        Log::info('Event updated successfully.', ['event' => $event]);

                        return response()->json($event);
                    } else {
                        return response()->json(['error' => 'Event not found'], 404);
                    }
                    break;

                case 'delete':
                    $event = Event::find($request->id);
                    if ($event) {
                        $event->delete();
                        Log::info('Event deleted successfully.', ['event' => $event]);

                        return response()->json(['message' => 'Event deleted']);
                    } else {
                        return response()->json(['error' => 'Event not found'], 404);
                    }
                    break;

                default:
                    return response()->json(['error' => 'Invalid request type'], 400);
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Error in AJAX event request: ' . $e->getMessage());
            return response()->json(['error' => 'Error processing request.'], 500);
        }
    }
}
