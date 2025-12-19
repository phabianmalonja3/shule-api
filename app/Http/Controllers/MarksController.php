<?php

namespace App\Http\Controllers;

use App\Exports\MarksTemplateExport;
use App\Exports\StudentMarksExport;
use App\Imports\MarksImport;
use App\Models\Mark;
use App\Models\StreamSubject;
use App\Models\StreamSubjectTeacher;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\ExamUpload;
use App\Models\Subject;
use App\Models\StudentResult;

class MarksController extends Controller
{
    private function downloadTemplate()
    {
        return Excel::download(new MarksTemplateExport, 'marks_template.xlsx');
    }

    public function showUploadForm(Request $request)
    {
        $teacher = Auth::user(); 
        $assignments = StreamSubjectTeacher::with(['stream.schoolClass', 'subject']) // Eager load stream, class, and subject
        ->where('teacher_id', $teacher->id) // Filter by the teacher ID
        ->get();

        if(!$assignments){
            return redirect()->back()->with('error','Sorry, currently there is no any subject assigned to a teacher.');
        }
        
        $subjects = $assignments->pluck('subject');
        $streams = $assignments->pluck('stream')->unique();
        $streams = $streams->sortBy(function ($stream) {
                                    return $stream->schoolClass->name; });

        $subjectsAssigned =$subjects->unique();
        $exam_types =\App\Models\ExaminationType::all();
        $selectedSubject = $request->selectedSubject ?? 0;
        $subjectStatus = $request->subjectStatus ?? 0;

        //  $results = StudentResult::where('exam_type_id', 8)
        //     ->where('month',9)
        //     ->orderByDesc('total_marks')
        //     ->get();

        // $studentsIds = $results->pluck('student_id')->values()->toArray();
        // $studentStreams = Student::whereIn('id',$studentsIds)->get();

        return view('marks.upload', compact('subjectsAssigned','streams','exam_types','selectedSubject','subjectStatus'));
    }

    public function showClassUploadForm(Request $request)
    {  
        $teacher = Auth::user();
        //$assignments = StreamSubjectTeacher::with(['stream.schoolClass', 'subject']) // Eager load stream, class, and subject
        //->where('teacher_id', $teacher->id) // Filter by the teacher ID
        //->get();

        $academicYear = AcademicYear::where('school_id', auth()->user()->school->id)
                                    ->where('is_active', true)
                                    ->first();
                       
        $assignments = StreamSubjectTeacher::with('subject')
                                        ->whereHas('stream', function ($query) use($academicYear){
                                            $query->where('school_class_id', $academicYear->school_id);})
                                        ->get();

        $subjects = $assignments->pluck('subject');
        //$streams = $assignments->pluck('stream')->unique();
        //$subjectsAssigned =$subjects->unique();
        $exam_types =\App\Models\ExaminationType::all();
        $exam_uploads = [];

        //return $exam_uploads;
        $classes = SchoolClass::where('school_id', $teacher->school->id)->get();
        $selectedSubject = $request->subject ?? 0;
        $subjectStatus = 0;

        return view('marks.academic_teacher_upload', compact('classes','exam_types','exam_uploads','selectedSubject','subjectStatus'));
    }

    public function index(Request $request)
    {   
        $class_result_flag =  $request->class_result_flag ?? null;
        $editStatus = $request->editStatus?? null; 
        $selectedexaminationsType = $request->selectedexaminationsType?? null; 
        $selectedClass = $request->selectedClass?? null; 
        $selectedSubject = $request->selectedSubject?? null; 
        $selectedStream = $request->selectedStream?? null; 
        $search = $request->search?? null;
        $accordionKey = $request->accordionKey?? null;  
        $subjectStatus = $request->subjectStatus?? null; 

        if($class_result_flag){
            $flag = explode('/', $class_result_flag);
            $selectedexaminationsType = $flag[1];
        }

        $mark = Mark::where('subject_id',$selectedSubject)->whereNull('position')
                    ->whereHas('student', function($q) use($selectedClass){$q->where('school_class_id',$selectedClass);})->get();

        if(count($mark) > 0){
            
            $uniqueMonths = $mark->pluck('month')->unique()->values()->toArray();

            $subject = $selectedSubject?? $mark->first()->subject_id;
            $examType = $selectedexaminationsType?? $mark->first()->exam_type_id;
            $academicYear = $mark->first()->academic_year_id;
            $this->updateSubjectPositions($subject,$examType,$academicYear,$uniqueMonths,$selectedClass);
        }

        return view('marks.marks_list',compact('class_result_flag','editStatus','selectedexaminationsType','selectedClass','selectedSubject','selectedStream','search','accordionKey','subjectStatus'));
    }

    public function updateSubjectPositions($selectedSubject, $examTypeId, $academic_year_id, $uniqueMonths, $selectedClass)
    {
        $allUpdates = [];

        foreach($uniqueMonths as $month){
            $marks = Mark::with(['student' => function($q) use($selectedClass){$q->where('school_class_id',$selectedClass);}])
                ->where('subject_id', $selectedSubject)
                ->where('exam_type_id', $examTypeId)
                ->where('academic_year_id', $academic_year_id)
                ->whereHas('student', function($q) use($selectedClass){$q->where('school_class_id',$selectedClass);})
                ->where('month', $month)
                ->orderBy('subject_id')
                ->orderByDesc('obtained_marks')
                ->get();

            $groupedResults = $marks->groupBy(function ($item) {
                return $item->subject_id . '-' . optional($item->student)->stream_id;
            });
            
            foreach ($groupedResults as $subjectStreamMarks) {
                $position = 1;
                $previousMarks = null;
                $currentRank = 0;
                foreach ($subjectStreamMarks as $mark) {
                    if ($mark->obtained_marks !== $previousMarks) {
                        $currentRank++;
                    }
                    $allUpdates[$mark->id]['position'] = $currentRank;
                    $previousMarks = $mark->obtained_marks;
                    $position++;
                }
            }
        }

        foreach ($allUpdates as $id => $data) {
            Mark::where('id', $id)->update($data);
        }
}

    public function getSubjectAssigned($teacherId)
    {
        return User::whereHas('subjects', function ($query) use ($teacherId) {
            // Only get students who have marks assigned by the specific teacher
            $query->where('teacher_id', $teacherId);
        })
        ->with(['subjects' => function ($query) use ($teacherId) {
            // Eager load the marks for the specific teacher
            $query->where('teacher_id', $teacherId);
        }])
        ->paginate(10);
    }

    public function upload(Request $request)
    {
        // dd('ok');

        $streamId = $request->input('stream_id');
        $subjectId = $request->input('subject_id');

        if ($request->has('download_template')) {
          return  Excel::download(new MarksTemplateExport($streamId,$subjectId), 'marks_template.xlsx');
        }else{
            $request->validate([
                'subject_id' => 'required|exists:subjects,id',
                'exam_type' => 'required|exists:examination_types,id',
                'marks_file' => 'required|file|mimes:xlsx,xls',
            ]);

            try {
                Excel::import(new MarksImport($request->subject_id, $streamId ,$request->exam_type),
                $request->file('marks_file'));

                return redirect()->route('marks.index')->with('success', 'Marks uploaded successfully!');
            } catch (\Exception $e) {
                Log::error("Error uploading marks: " . $e->getMessage());
                return back()->withErrors(['error' => 'Error uploading marks. Please check the uploaded file and try again.'. $e->getMessage()]);
            }
        }

    }

    public function resetMarksUpload(Request $request)
    {
        ExamUpload::where('id', $request->exam_upload_id)->delete();
        return back()->with('success','Exam upload reset successfully.');
    }
}
