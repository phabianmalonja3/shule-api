<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class AttendencyController extends Controller
{


    public function dailyIndex(Request $request)
    {
        $teacher = auth()->user();
        // Get the authenticated user's streams
        $date = $request->input('date', now()->format('Y-m-d'));


        $streams = $teacher->streams->unique();

        // Get the stream ID from the request (if any)
        $selectedStream = $request->input('stream');


        // If a stream is selected, filter the students by that stream
        if ($selectedStream > 0) {

            // dd($selectedStream);
            // Filter students by the selected stream
            $students = Student::with('user')
                ->where('stream_id', $selectedStream)
                ->get();

            $attendances = Attendance::where('stream_id', $selectedStream)
                ->whereDate('date', $request->input('date', now()->today()))
                ->get();


            // dd($students);
        } else {
            // Otherwise, get all students
            // $students = Student::with('user')
            //     ->whereIn('stream_id',   $streams->pluck('id'))
            //     ->get();
            $students = [];
            // dd($students);
            $attendances = [];
        }

        // Get today's date
        $today = Carbon::today();
        // dd()
        // Return the view with the filtered students
        return view('attendance.daily.index', compact('students', 'today', 'attendances', 'streams', 'date'));
    }
    public function dailyCalanderStudent(Request $request)
    {
       
        return view('attendance.monthly.student-attendancy');
    }



    public function dailyStore(Request $request)
    {
        // dd($request->all());

        $request->validate([
            'date' => 'required|date',
            'stream' => 'required|exists:streams,id',
            'attendance' => 'required|array',
            'attendance.*' => 'required|in:present,absent,late,excused',
            'reason.*' => 'nullable|string|max:255',
        ]);

        $date = $request->input('date');
        $today = Carbon::parse($date);


        foreach ($request->input('attendance') as $studentId => $status) {
            $reason = $request->input('reason.' . $studentId);

            $attandance = Attendance::updateOrCreate(
                ['student_id' => $studentId, 'date' => $date, 'stream_id' => $request->stream, 'teacher_id' => auth()->id()],

                ['status' => $status, 'reason' => $reason]
            );
        }

        $absentStudents = Attendance::where('status', 'absent')
            ->where('date', $date)->get();

        // dd($absentStudents);


        if ($absentStudents) {

            foreach ($absentStudents as $student) {

                // dd($student->parents);
                // sms send to the parent for abscent student.....
            }
        }


        //  dd($attandance);

        // Set a session flag to prevent further submissions today
        //  Session::put('attendance_taken_' . $today->format('Y-m-d'), true);
        flash()->option('position', 'bottom-right')->success('Attendance recorded successfully.');

        return redirect()->route('attendance.daily.index', ['date' => $date, 'stream' => $request->stream]);
    }



    public function weeklyIndex()
    {

        $teacher = auth()->user();
        $students = Student::with('user')->get(); // Or filter students as needed
        $startDate = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endDate = Carbon::now()->endOfWeek(Carbon::SUNDAY);


        $streams = $teacher->streams->unique('id');


        return view('attendance.weekly.index', compact('students', 'startDate', 'endDate', 'streams'));
    }
    public function monthlyIndex()
    {

        $teacher = auth()->user();
        $students = Student::with('user')->get(); // Or filter students as needed


        $streams = $teacher->streams->unique('id');


        return view('attendance.monthly.index', compact('streams'));
    }

    public function attandanceShow(Student $student)
    {


       

        return view('attendance.calander',compact('student'));
    }
    public function weeklyReport(Request $request)
    {


        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'stream' => 'nullable|exists:streams,id', // Validate stream if provided
        ]);

        $startDate = Carbon::parse($request->input('start_date'));
        $endDate = Carbon::parse($request->input('end_date'));
        $selectedStreamId = $request->input('stream');

        // Get the teacher and their unique streams
        $teacher = auth()->user();
        $streams = $teacher->streams->unique('id');

        // Get students filtered by stream if a stream is selected
        $students = Student::with('user')
            ->when($selectedStreamId, function ($query) use ($selectedStreamId) {
                $query->where('stream_id', $selectedStreamId);
            })
            ->get();

        // Get attendances filtered by date range and optionally by stream
        $attendances = Attendance::whereBetween('date', [$startDate, $endDate])
            ->when($selectedStreamId, function ($query) use ($selectedStreamId) {
                $query->whereHas('student', function ($query) use ($selectedStreamId) {
                    $query->where('stream_id', $selectedStreamId);
                });
            })
            ->get()
            ->groupBy('student_id');

        return view('attendance.weekly.report', compact('students', 'streams', 'selectedStreamId', 'startDate', 'endDate', 'attendances'));
    }

    public function showMonthlyReport(Request $request)
    {
        $streamId = $request->input('stream');
        $month = $request->input('month');
        $year = $request->input('year');

        $stream = Stream::findOrFail($streamId);
        $students = Student::with('user')
            ->when($streamId, function ($query) use ($streamId) {
                $query->where('stream_id', $streamId);
            })
            ->get();
        $totalDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $monthName = date('F', mktime(0, 0, 0, $month, 10));

        foreach ($students as $student) {
            $student->presentDays = 0;

            for ($day = 1; $day <= $totalDays; $day++) {
                $date = date('Y-m-d', strtotime("$year-$month-$day"));
                $attendance = $student->attendances()->where('date', $date)->first();

                if ($attendance && $attendance->status == 'present') {
                    $student->presentDays++;
                }
            }

            $student->attendancePercentage = ($student->presentDays / $totalDays) * 100;
        }

        return view('attendance.monthly.report', compact('stream', 'students', 'totalDays', 'month', 'year', 'monthName'));
    }
}
