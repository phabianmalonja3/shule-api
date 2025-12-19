<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Mark extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'marks';
    protected $fillable = 
    [
        'student_id',
        'exam_result_id',
        'teacher_id',
        'subject_id',
        'obtained_marks',
        'grade',
        'month',
        'week',
        'remark',
        'academic_year_id',
        'exam_type_id',
        'exam_upload_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::saved(function ($mark) {
            self::updateStudentResult($mark->student_id, $mark->exam_type_id,$mark->academic_year_id,$mark->month,$mark->week);
        });

        static::deleted(function ($mark) {
            self::updateStudentResult($mark->student_id, $mark->exam_type_id,$mark->academic_year_id,$mark->month,$mark->week);
        });
    }

    public static function updateStudentResult($studentId, $examTypeId, $academic_year_id, $month, $week)
    {
        $query = self::where('student_id', $studentId)
            ->where('exam_type_id', $examTypeId)
            ->where('academic_year_id', $academic_year_id)
            ->when($month, fn($q) => $q->where('month', $month))
            ->when($week, fn($q) => $q->where('week', $week));
        
        $results = $query->selectRaw('SUM(obtained_marks) as total_marks, AVG(obtained_marks) as average_marks')->first();

        StudentResult::updateOrCreate(
            [
                'student_id' => $studentId,
                'exam_type_id' => $examTypeId,
                'academic_year_id' => $academic_year_id,
                'month' => $month,
                'week' => $week,
            ],
            [
                'total_marks' => $results->total_marks ?? 0,
                'average_marks' => $results->average_marks ?? 0,
            ]
        );

        self::updatePositions($examTypeId, $academic_year_id, $month, $week);
        //self::updateSubjectPositions($examTypeId,$academic_year_id,$month);
    }

    public static function updatePositions($examTypeId, $academic_year_id, $month, $week)
    {
        // Use a single query to get all students' results for the exam
        $query = StudentResult::with('student')
            ->where('exam_type_id', $examTypeId)
            ->where('academic_year_id', $academic_year_id)
            ->when($month, fn($q) => $q->where('month', $month))
            ->when($week, fn($q) => $q->where('week', $week));

        $results = $query->orderByDesc('total_marks')->get();
        $allUpdates = [];

        // 1. Calculate overall class position
        $position = 1;
        $previousMarks = null;
        $currentRank = 0;

        $gradeScales = GradeScale::where('school_id', auth()->user()->school_id)->get();

        foreach ($results as $result) {
            $gradeScale = $gradeScales->first(function ($scale) use ($result) {
                    return $result->average_marks >= $scale->min_marks && $result->average_marks <= $scale->max_marks;
                });
            
            $newGrade = $gradeScale ? $gradeScale->grade : 'F';
            $newRemark = !empty($result->remark) ? $result->remark : ($gradeScale ? $gradeScale->remarks : 'Fail');

            if ($result->total_marks !== $previousMarks) {
                $currentRank = $position;
            }
            $allUpdates[$result->id]['position'] = $currentRank;
            $allUpdates[$result->id]['grade'] = $newGrade;
            $allUpdates[$result->id]['remark'] = $newRemark;
            $previousMarks = $result->total_marks;
            $position++;
        }

        // 2. Calculate stream-specific position
        $streamGroupedResults = $results->groupBy(function ($item) {
            return optional($item->student)->stream_id;
        });

        foreach ($streamGroupedResults as $streamResults) {
            $streamPosition = 1;
            $streamPreviousMarks = null;
            $streamCurrentRank = 0;
            foreach ($streamResults as $result) {
                if ($result->total_marks !== $streamPreviousMarks) {
                    $streamCurrentRank = $streamPosition;
                }
                $allUpdates[$result->id]['stream_position'] = $streamCurrentRank;
                $streamPreviousMarks = $result->total_marks;
                $streamPosition++;
            }
        }

        // 3. Perform a single batch update
        foreach ($allUpdates as $id => $data) {
            StudentResult::where('id', $id)->update($data);
        }
    }

    // public static function updateSubjectPositions($examTypeId, $academic_year_id, $month)
    // {
    //     $allUpdates = [];

    //     $marks = self::with('student')
    //         ->where('exam_type_id', $examTypeId)
    //         ->where('academic_year_id', $academic_year_id)
    //         ->when($month, fn($q) => $q->where('month', $month))
    //         ->orderBy('subject_id')
    //         ->orderByDesc('obtained_marks')
    //         ->get();

    //     $groupedResults = $marks->groupBy(function ($item) {
    //         return $item->subject_id . '-' . optional($item->student)->stream_id;
    //     });
        
    //     foreach ($groupedResults as $subjectStreamMarks) {
    //         $position = 1;
    //         $previousMarks = null;
    //         $currentRank = 0;
    //         foreach ($subjectStreamMarks as $mark) {
    //             if ($mark->obtained_marks !== $previousMarks) {
    //                 $currentRank++;
    //             }
    //             $allUpdates[$mark->id]['position'] = $currentRank;
    //             $previousMarks = $mark->obtained_marks;
    //             $position++;
    //         }
    //     }

    //     foreach ($allUpdates as $id => $data) {
    //         Mark::where('id', $id)->update($data);
    //     }
    // }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function examType()
    {
        return $this->belongsTo(ExaminationType::class, 'exam_type_id');
    }
    
    public function result()
    {
        return $this->belongsTo(StudentResult::class, 'exam_result_id');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function examinationResult()
    {
        return $this->belongsTo(ExaminationResult::class, 'exam_result_id');
    }
}
