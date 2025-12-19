<?php

namespace App\Http\Controllers\Api\V1;

use Spatie\Pdf\Pdf;
use App\Models\Note;
use App\Models\User;
use App\Models\Stream;
use App\Models\Student;
use App\Models\Homework;
use App\Models\Resource;
use App\Models\Assignment;
use App\Models\SchoolClass;
use Illuminate\Http\Request;
use App\Models\StreamSubject;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\StreamSubjectTeacher;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Resources\StudentResource;
use PHPUnit\Framework\TestStatus\Notice;
use App\Http\Resources\StudentCollection;
use App\Models\Combination;
use App\Models\ParentModel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Console\View\Components\Alert;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class StudentController extends Controller
{

    public function index()
    {
        return view('student.list');
    }

    public function show($id)
    {
        $student = Student::with(['schoolClass', 'stream'])->findOrFail($id);
        return view('student.show', compact('student'));
    }

    public function panel()
    {
        return view('student.index');
    }

    private function exportStudentsToExcel($studentsQuery, $streamId)
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = "student_list_{$timestamp}.xlsx";
    
        $students = $streamId
            ? $studentsQuery->where('stream_id', $streamId)->get()
            : $studentsQuery->get();
    
        return Excel::download(new StudentsExport($students), $filename);
    }

    public function create(Request $request)
    {
        $teacher = auth()->user();
        
        $teacherId = $teacher->id;


        $assignments = StreamSubjectTeacher::with(['stream.schoolClass', 'subject'])
            ->where('teacher_id', $teacherId)
            ->get();
        
        // Extract unique class IDs
        $class = $assignments->pluck('stream.schoolClass')->unique()->values()->all();
        
        
        if($teacher->hasAnyRole(['academic teacher','header teacher'])){
            $streams = SchoolClass::with('streams')
                            ->latest()
                            ->where('school_id', auth()->user()->school_id)
                            ->get();

        $nestedStreams = $streams->pluck('streams');
        $streams = $nestedStreams->flatten(1);

        }elseif($teacher->hasRole('class teacher')){
            $streams = $teacher->streams;

        }

        // Prepare data for output
        $data = [
            'class' => $class, // List of unique class IDs
            // 'class_count' => count($class), // Number of unique classes
            'stream_count' => $assignments->pluck('stream.id')->unique()->count(),
            'subject_count' => $assignments->pluck('subject.id')->unique()->count(),
            'assignments' => $assignments, // Detailed assignments
        ];
        

        // dd($request->classid);

        return view('student.create',['school_class_id'=>$request->classid,'streams'=>$streams,'class'=>$class]);
    }

    public function edit($id)
    {
        $currentYear = Carbon::now()->year;
        $student = Student::with(['parents','combinations'=>function ($query) use ($currentYear) {
                $query->where(DB::raw('YEAR(student_combination.created_at)'), $currentYear)->orderByDesc('updated_at');
            }])->findOrFail($id);
        $teacherId = auth()->user()->id;

        $assignments = StreamSubject::with(['stream.class', 'subject'])
            ->where('teacher_id', $teacherId)
            ->get();

        // Extract unique class IDs
        $class = $assignments->pluck('stream.class')->unique()->values()->all();
        $streams = $assignments->pluck('stream')->unique();
        $combinations = Combination::all();

        return view('student.edit', compact('student', 'class', 'streams','combinations'));
    }

    public function store(Request $request)
    {   
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'phone' => ['nullable', 'regex:/^0[0-9]{9}$/', 'unique:users,phone'],
            'surname' => 'required|string|max:255',
            'email' => ['nullable', 'email', 'unique:users,email'],
            
            'stream_id' => 'required|exists:streams,id',
            'gender' => ['required', 'in:male,female'],
        ]);

        $middlename = ($validated['middle_name'] ?? '');
        $fullname = "{$validated['first_name']} {$middlename} {$validated['surname']}";
        $schoolId = Auth::user()->school_id;
        $year = now()->year;

        $existingStudent = User::where('name', $fullname)
            ->whereHas('student', function ($query) use ($schoolId) {
                $query->where('school_id', $schoolId);
            })
            ->first();

        if ($existingStudent) {
        flash()->option('position', 'bottom-right')->error("Student '{$fullname}' already exists in the system.");

            return back();
        }
        
        $stream = Stream::findorfail( $validated['stream_id']);
        $classId = $stream->schoolClass->id;
    
        $existingStudentInClass = Student::where('school_id', $schoolId)
            ->where('school_class_id', $classId)
            ->where('stream_id', $validated['stream_id'])
            ->whereHas('user', function ($query) use ($fullname) {
                $query->where('name', $fullname);
            })
            ->first();

        if ($existingStudentInClass) {
        flash()->option('position', 'bottom-right')->error("Student '{$fullname}' is already assigned to this class and stream.");

            return back();
        }

        try{

            $lastId = Student::where('school_id', $schoolId)->max('id');
            $nextNumber = sprintf('%04d', $lastId + 1);
            $registrationNumber = "{$nextNumber}/{$schoolId}/{$year}";
            $academic_year_id = $stream->schoolClass->academic_year_id;
        
            // $
            // Use a transaction to ensure atomicity
            DB::beginTransaction();
                // Create user for the student
                $user = User::create([
                    'name' => $fullname,
                    'phone' => $validated['phone'],
                    'username' => $registrationNumber,
                    'gender' => $validated['gender'],
                    'is_active' => true,
                    'password' => 'password', // Use hashed password
                ]);
        
                // Assign 'student' role
        
                $user->assignRole('student');
        
                // Create student record
            $student=  Student::create([
                    'user_id' => $user->id,
                    'school_id' => $schoolId,
                    'school_class_id' => $classId,
                    'academic_year_id'=>$academic_year_id,
                    'stream_id' => (int)$validated['stream_id'],
                    'reg_number' => $registrationNumber,
                    'created_by' => auth()->user()->id
                ]);
            
                DB::commit();    

                flash()->option('position', 'bottom-right')->success('Student created successfully!');
                return redirect()->route('students.index');

        }catch(\Exception $e){

            DB::rollBack();
            flash()->option('position', 'bottom-right')->error('Student created successfully!');

            return back();
        }
    }

    public function update(Request $request, $id)
    { 
        $student = Student::findOrFail($id);
        $user = $student->user; 

        $rules = [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'required|string|max:255',
            'phone' => ['nullable', 'regex:/^0[0-9]{9}$/'],
            'surname' => 'required|string|max:255',
            'school_class_id' => 'required|exists:school_classes,id',
            'stream_id' => 'required|exists:streams,id',
            'gender' => ['required', 'in:male,female'],
            'combination' => 'required'
        ];

        if ($request->has('parents') && is_array($request->parents)) {
                
            foreach ($request->parents as $parentId => $parentData) {
                
                // Get the ParentModel instance for the current student/parent relationship
                $parentModel = $student->parents->where('id', $parentId)->first();
                $parentUserId = $parentModel->user_id ?? null;

                // Base rules for each parent entry
                $parentRules["parents.{$parentId}.first_name"]   = 'required|string|max:255';
                $parentRules["parents.{$parentId}.middle_name"]  = 'nullable|string|max:255';
                $parentRules["parents.{$parentId}.surname"]      = 'required|string|max:255';
                $parentRules["parents.{$parentId}.gender"]       = ['required', 'in:male,female'];
                
                $parentRules["parents.{$parentId}.relationship"] = [
                    'required', 
                    Rule::in(['parent', 'aunt', 'uncle', 'sibling', 'grandmother', 'grandfather', 'sponsor', 'other'])
                ];
                
                // 3. Unique Phone Rule for Parent User
                // Check if the input phone number is different from the currently stored phone number.
                if ($parentModel && ($parentModel->phone != $parentData['phone'])) {
                    
                    // Apply the unique rule, ignoring the current parent's User ID
                    $parentRules["parents.{$parentId}.phone"] = [
                        'nullable', 
                        'regex:/^0[0-9]{9}$/', 
                        Rule::unique('users', 'phone')->ignore($parentUserId),
                    ];
                } else {
                    // Keep the simple validation if phone hasn't changed or model is missing
                    $parentRules["parents.{$parentId}.phone"] = ['nullable', 'regex:/^0[0-9]{9}$/'];
                }
            }
            
            // 4. Merge all rules
            $rules = array_merge($rules, $parentRules);
        }

        $messages = [
            'parents.*.first_name.required' => 'Parent\'s first name is required.',
            'parents.*.surname.required' => 'Parent\'s surname is required.',
            'parents.*.gender.required' => 'Parent\'s gender is required.',
            'parents.*.relationship.required' => 'Parent\'s relationship to student is required.',                        
            'parents.*.phone.unique' => 'The phone number has already been taken.',
        ];

        $validated = $request->validate($rules, $messages);
        $fullname = trim("{$validated['first_name']} {$validated['middle_name']} {$validated['surname']}");
    
        $existingStudent = User::where('name', $fullname)
            ->whereHas('student', function ($query) use ($student) {
                $query->where('school_id', $student->school_id);
            })
            ->where('id', '!=', $user->id)
            ->first();
    
        if ($existingStudent){
            flash()->option('position', 'bottom-right')->error("Student '{$fullname}' already exists in the system.");
            return back();
        }

        DB::transaction(function () use ($validated, $user, $student, $fullname) {

            $student_phone = $validated['parents'][0]['phone']?? $validated['phone'];
            $user->update([
                'name'          => $fullname,
                'phone'         => $student_phone,
                'gender'        => $validated['gender']
            ]);
    
            $student->update([
                'school_class_id'   => $validated['school_class_id'],
                'stream_id'         => $validated['stream_id']
            ]);

            $combination = Combination::findOrFail($validated['combination']);
            $studentinCombination = $combination->students();


$studentinCombination->sync([
    $student->id => [
        'created_by' => Auth::id()
    ]
]);
            if (isset($validated['parents'])) {
                foreach ($validated['parents'] as $parentId => $parentValidatedData) {
                    $parentModel = $student->parents->where('id', $parentId)->first();
                    
                    if ($parentModel) {
                        $parentUser = User::findOrFail($parentModel->user_id);
                        $parentFullname = trim("{$parentValidatedData['first_name']} {$parentValidatedData['middle_name']} {$parentValidatedData['surname']}");

                        // Update Parent's User Record (used for login/contact)
                        $parentUser->update([
                            'name'      => $parentFullname,
                            'phone'     => $parentValidatedData['phone'],
                            'gender'    => $parentValidatedData['gender'],
                            'username'  => $parentValidatedData['phone'],
                        ]);

                        // Update Parent Record (metadata linking them to the student)
                        $parentModel->update([
                            'first_name'    => $parentValidatedData['first_name'],
                            'middle_name'   => $parentValidatedData['middle_name'],
                            'sur_name'      => $parentValidatedData['surname'],
                            'gender'        => $parentValidatedData['gender'],
                            'phone'         => $parentValidatedData['phone'],
                            'relationship'  => $parentValidatedData['relationship'],
                        ]);
                    }
                }
            }            
        });
    
        flash()->option('position', 'bottom-right')->success('Student details updated successfully.');
    
        return redirect()->route('students.index');
    }

    public function assignCombination(Request $request)
    {
        $request->validate([
            'combination_id' => 'required|exists:combinations,id',
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id', 
        ], [
            'combination_id.required' => 'Please choose a combination to assign.',
            'student_ids.required' => 'No students were selected for assignment.',
        ]);

        $combinationId = $request->input('combination_id');
        $studentIds = $request->input('student_ids');
        $studentsCount = count($studentIds);
        $currentUserId = Auth::id();

        try {
            $combination = Combination::findOrFail($combinationId);

            DB::beginTransaction();

            $pivotData = [
                'created_by' => $currentUserId,
            ];

            $students = $combination->students();

            $students->attach($studentIds, $pivotData);

            DB::commit();

            $attachCount = $studentsCount;

            flash()->option('position', 'bottom-right')->success("Successfully assigned $attachCount student(s) to $combination->name combination for the current academic year.");
            return redirect()->back();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error attaching combination to students: ' . $e->getMessage());

            flash()->option('position', 'bottom-right')->error('An error occurred while assigning the combination. Please check the logs.');
            return redirect()->back();
        }
    }

    // Deactivate/Activate a student
    public function toggleActivation(Student $student)
    {
        $student->is_active = !$student->is_active;
        $student->save();

        return back();
    }

    // Delete (Optional)
    public function destroy(Student $student)
    {
        $student->user->delete();
        flash()->option('position', 'bottom-right')->success('Student deleted successfully.');

        return back();
    }

    public function downloadStudentSample()
    {
        $data = new Collection([
            [
                'first_name' => 'Jackson',
                'middle_name' => 'Michael',
                'sur_name' => 'Jaga',
                'gender' => 'Male',

            ],
            [
                'first_name' => 'Marry',
                'middle_name' => 'Mabrouk',
                'sur_name' => 'Osama',
                'gender' => 'Female ',
                
            ],
            [
                'first_name' => 'Agnes',
                'middle_name' => 'Isaac',
                'sur_name' => 'Owawa',
                'gender' => 'Female',
                
            ],
            [
                'first_name' => 'Beatus',
                'middle_name' => 'Hassan',
                'sur_name' => 'Kassim',
                'gender' => 'Female ',
            
            ],
        ]);

        return Excel::download(new class($data) implements FromCollection, WithHeadings ,ShouldAutoSize{
            private $data;

            public function __construct(Collection $data)
            {
                $this->data = $data;
            }

            public function collection()
            {
                return $this->data;
            }

            public function headings(): array
            {
                return [
                    'First Name',
                    'Middle Name',
                    'Surname',
                    'Gender',
                ];
            }
        }, 'students_sample.'.now().'.xlsx');
    }



public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048', // file size limit 2MB
        'stream_id' => 'required|exists:streams,id',
        // 'class_id' => 'required|exists:school_classes,id',
    ]);


    $stream = Stream::findorfail($request->stream_id);
    $streamId = $request->stream_id;
    $classId = $stream->schoolClass->id;

    try {
        $file = $request->file('file')->store('files');
        $uploadedFile = $request->file('file');
        $excelData = Excel::toCollection(new StudentsImport($streamId, $classId), $uploadedFile);

        $rows = $excelData->first();
        $headers = $rows->first()->keys()->toArray();
        $requiredHeaders = ['first_name', 'middle_name', 'surname', 'gender'];
        if (array_diff($requiredHeaders, $headers)) {
            flash()->option('position', 'bottom-right')->error("The uploaded file is missing 'first name', 'middle name', 'surname', or 'gender' column.");
            return redirect()->back();

        }

        Excel::import(new StudentsImport($streamId, $classId), $file);

        flash()->option('position', 'bottom-right')->success('New students uploaded successfully!');
        return redirect()->route('students.index');
    } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
        $failures = $e->failures();

        $errorMessages = [];
        foreach ($failures as $failure) {
            $errorMessages[] = "{$failure->errors()[0]}";
        }
        flash()->option('position', 'bottom-right')->error(implode('<br>', $errorMessages));
        return redirect()->back();
    } catch (\Exception $e) {
    flash()->option('position', 'bottom-right')->error($e->getMessage());
        return redirect()->back();
    }
}

}