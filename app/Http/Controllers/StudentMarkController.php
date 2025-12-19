<?php

namespace App\Http\Controllers;

use App\Models\Mark;
use App\Models\Student;
use App\Models\GradeScale;
use App\Models\ExaminationType;
use Illuminate\Http\Request;

class StudentMarkController extends Controller
{
    public function edit($studentId, $marksId, Request $request)
    { 
        $student = Student::findOrFail($studentId);
        $marks = Mark::where('id', $marksId)->where('student_id', $studentId)->get();

        return view('marks.edit', compact('student','marks','request'));
    }

    public function editClassMarks($studentId, $academicYearId, $examTypeId, Request $request)
    {   
        $student = Student::with('user:id,name')->findOrFail($studentId);
        $examinationType = examinationType::findOrFail($examTypeId);
        if($examinationType->name == 'Monthly'){
            $marks = Mark::where('student_id', $studentId)->where('academic_year_id',$academicYearId)->where('exam_type_id',$examTypeId)->where('month',$request->month)->get();
        }else{
            $marks = Mark::where('student_id', $studentId)->where('academic_year_id',$academicYearId)->where('exam_type_id',$examTypeId)->get();
        }

        return view('marks.edit', compact('student', 'marks','request'));
    }

    public function update($studentId, $marksId, Request $request)
    {
        $messages = [
            'obtained_marks.min' => 'Marks should not be below 0.',
            'obtained_marks.max' => 'Marks should not exceed 100.',
        ];

        $validated = $request->validate([
            'subject_id' => 'required|integer|exists:subjects,id',
            'obtained_marks' => 'required|integer|min:0|max:100',
            'remark' => 'nullable|string',
        ], $messages);

        $mark = Mark::find($marksId);
        
        if($mark){
            $gradeScales = GradeScale::where('school_id', auth()->user()->school_id)->get();
            $gradeScale = $gradeScales->first(function ($scale) use ($validated) {
                return $validated['obtained_marks'] >= $scale->min_marks && $validated['obtained_marks'] <= $scale->max_marks;
            });

            $newGrade = $gradeScale ? $gradeScale->grade : 'F';
            $newRemark = $validated['remark'] ?? ($gradeScale ? $gradeScale->remarks : 'Fail');

            $mark->update([
                'obtained_marks' => $validated['obtained_marks'],
                'grade' => $newGrade,
                'remark' => $newRemark,
            ]);

            $this->updateSubjectPositions($mark->subject_id, $mark->exam_type_id, $mark->academic_year_id, $mark->month);
            flash()->option('position', 'bottom-right')->success('Marks updated successfully.');

        }else{
            flash()->option('position', 'bottom-right')->error('Something is wrong.');
        }

        $requestParameters = $request->only([
            'editStatus', 'selectedexaminationsType', 'selectedClass',
            'selectedSubject', 'selectedStream', 'search','accordionKey'
        ]);
// return $requestParameters;
        return redirect()->route('marks.index', $requestParameters);
    }

    public function updateClassMarks(Request $request)
    {
        $messages = [
            'marks.*.obtained_marks.min' => 'Marks should not be below 0.',
            'marks.*.obtained_marks.max' => 'Marks should not exceed 100.',
        ];

        $validated = $request->validate([
            'marks' => 'required|array',
            'marks.*.id' => 'required|integer|exists:marks,id',
            'marks.*.obtained_marks' => 'required|integer|min:0|max:100', 
            'marks.*.subject_id' => 'required|integer|exists:subjects,id',
            'marks.*.remark' => 'nullable|string',
        ], $messages);

        $gradeScales = GradeScale::where('school_id', auth()->user()->school_id)->get();
        $subjectIdsToUpdate = [];

        foreach ($validated['marks'] as $markData) {
            $mark = Mark::find($markData['id']);
            if ($mark) {
                $gradeScale = $gradeScales->first(function ($scale) use ($markData) {
                    return $markData['obtained_marks'] >= $scale->min_marks && $markData['obtained_marks'] <= $scale->max_marks;
                });
                
                $newGrade = $gradeScale ? $gradeScale->grade : 'F';
                $newRemark = !empty($markData['remark']) ? $markData['remark'] : ($gradeScale ? $gradeScale->remarks : 'Fail');

                if ($mark->obtained_marks != $markData['obtained_marks'] || $mark->grade != $newGrade || $mark->remark != $newRemark) {
                    $mark->update([
                        'obtained_marks' => $markData['obtained_marks'],
                        'grade' => $newGrade,
                        'remark' => $newRemark,
                    ]);

                    $subjectIdsToUpdate[] = [
                        'subject_id' => $mark->subject_id,
                        'exam_type_id' => $mark->exam_type_id,
                        'academic_year_id' => $mark->academic_year_id,
                        'month' => $mark->month,
                    ];
                }
            }
        }

        foreach (array_unique($subjectIdsToUpdate, SORT_REGULAR) as $data) {

            $this->updateSubjectPositions($data['subject_id'], $data['exam_type_id'], $data['academic_year_id'], $data['month']);
        }

        $requestParameters = $request->only([
            'class_result_flag', 'editStatus', 'examTypeId', 'selectedClass',
            'selectedSubject', 'selectedStream', 'search','accordionKey'
        ]);

        flash()->option('position', 'bottom-right')->success('Marks updated successfully.');

        return redirect()->route('marks.index', $requestParameters);
    }

    // public function updateClassMarks(Request $request)
    // {   
    //     $messages = [
    //         'marks.*.obtained_marks.min' => 'Marks should not be below 0.',
    //         'marks.*.obtained_marks.max' => 'Marks should not exceed 100.',
    //     ];

    //     $validated = $request->validate([
    //         'marks' => 'required|array',
    //         'marks.*.id' => 'required|integer|exists:marks,id',
    //         'marks.*.obtained_marks' => 'required|integer|min:0|max:100', 
    //         'marks.*.subject_id' => 'required|integer|exists:subjects,id',
    //         'marks.*.remark' => 'nullable|string',
    //     ], $messages);

    //     $gradeScales = GradeScale::where('school_id', auth()->user()->school_id)->get();

    //     foreach ($validated['marks'] as $markData) {
    //         $mark = Mark::findOrFail($markData['id']);

    //         if($mark->obtained_marks != $markData['obtained_marks']){
    //             $gradeScale = $gradeScales->first(function ($scale) use ($markData) {
    //                 return $markData['obtained_marks'] >= $scale->min_marks && $markData['obtained_marks'] <= $scale->max_marks;
    //             });

    //             $mark->update([
    //                 'obtained_marks' => $markData['obtained_marks'],
    //                 'grade' => $gradeScale ? $gradeScale->grade : 'F',
    //                 'remark' => $gradeScale ? $gradeScale->remarks : 'Fail',
    //             ]);

    //             $this->updateSubjectPositions($mark->subject_id, $mark->exam_type_id, $mark->academic_year_id, $mark->month);
    //         }
    //     }

    //     $requestParameters = $request->only([
    //         'class_result_flag', 'editStatus', 'examTypeId', 'selectedClass',
    //         'selectedSubject', 'selectedStream', 'search','accordionKey'
    //     ]);

    //     flash()->option('position', 'bottom-right')->success('Marks updated successfully.');

    //     return redirect()->route('marks.index', $requestParameters);
    // }

    public function updateSubjectPositions($selectedSubject, $examTypeId, $academic_year_id, $month)
    { 
        $allUpdates = [];

        $marks = Mark::with('student')
            ->where('exam_type_id', $examTypeId)
            ->where('subject_id', $selectedSubject)
            ->where('academic_year_id', $academic_year_id)
            ->when($month, fn($q) => $q->where('month', $month))
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

        foreach ($allUpdates as $id => $data) {
            Mark::where('id', $id)->update($data);
        }
    }
}
