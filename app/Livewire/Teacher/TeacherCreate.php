<?php

namespace App\Livewire\Teacher;

use App\Models\User;
use Livewire\Component;
use App\Imports\UsersImport;
use Livewire\WithFileUploads;
use App\Imports\TeacherImport;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class TeacherCreate extends Component
{
    use WithFileUploads;

    public  $first_name,
            $middle_name,
            $sur_name,
            $email ,
            $gender ,
            $phone ,
            $users_excel ,
            $role ,
            $isUploading= false;
            
    protected $rules =[
        'first_name' => 'required|string|max:255',
        'middle_name' => ['nullable', 'string','max:255'],
        'sur_name' => 'required|string|max:255',
        'email' => ['nullable', 'email', 'unique:users,email'],
        'role' => ['nullable', 'exists:roles,name'],
        'phone' => ['required', 'regex:/^0[0-9]{9}$/', 'unique:users,phone'],
        'gender' => 'required|in:male,female',

    ];

    protected $messages = [
        'first_name.required' => "Teacher's first name is required.",
        'sur_name.required' => "Teacher's surname is required.",
        'phone.required' => "Teacher's phone number is required.",
        'phone.unique' => "The phone number specified has already been used.",
        'gender.required' => "Teacher's gender is required.",
    ];

    public function downloadSample()
    {
        $data = new Collection([
            ['firstname' => 'Julieth','middlename' => 'Michael','surname' => 'Denilson', 'phone' => '074567890','gender'=>'Female'],
            ['firstname' => 'Ezekiel','middlename' => 'Wilson','surname' => 'Wright', 'phone' => '074567890','gender'=>'Male']

        ]);

        return Excel::download(new class($data) implements FromCollection, WithHeadings,ShouldAutoSize {
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
                    'Phone', 
                    'Gender', 
                ];
            }
        }, 'teachers_template.'.now().'.xlsx');
    }
      
    public function render()
    {
        $excludedRoles = ['header teacher','head teacher', 'administrator', 'parent', 'student','class teacher'];
        
        $schoolId = Auth::user()->school_id;
    
        $academicTeacherCount = \App\Models\User::whereHas('roles', function ($query) {
            $query->where('name', 'academic teacher');
        })->where('school_id', $schoolId)->count();


        $roles = Role::whereNotIn('name', values: $excludedRoles)->get();


        $academicTeacherCount = User::where('school_id', auth()->user()->school_id)
        ->whereHas('roles', function ($query) {
            $query->where('name', 'academic teacher');
        })
        ->count();
        $HeaderTeacherAssistCount = User::where('school_id', auth()->user()->school_id)
        ->whereHas('roles', function ($query) {
            $query->where('name', 'assistant headteacher');
        })
        ->count();
        return view('livewire.teacher.teacher-create',compact('academicTeacherCount','roles','HeaderTeacherAssistCount'));
    }

    public function import()
    {
        $this->isUploading =true;
        $this->resetValidation();
        $this->validate([
            'users_excel' => 'required|mimes:xlsx,xls|max:2048',
        ]);

        $file = $this->users_excel;

        $filePath = $file->getRealPath();

        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray();
        $header = array_shift($rows);

        if  (empty($rows)) {
            flash()->option('position','bottom-right')->error("The uploaded file is empty or contains no records.");
            return redirect()->back();
        }

        $cleanedHeader = array_map(function($value) {
            return strtolower(trim($value ?? ''));
        }, $header);

        $filteredHeader = array_filter($cleanedHeader, function($value) {
            return $value !== null && $value !== '';
        });

        $firstName = array_search('first name', array_map('strtolower', $filteredHeader));
        $middleName = array_search('middle name', array_map('strtolower', $filteredHeader));
        $surname = array_search('surname', array_map('strtolower', $filteredHeader));
        $phoneIndex = array_search('phone', array_map('strtolower', $filteredHeader));
        $genderIndex = array_search('gender', array_map('strtolower', $filteredHeader));

        if ($firstName === false || $middleName === false || $surname === false || $phoneIndex === false || $genderIndex === false) {
            flash()->option('position', 'bottom-right')->error("The uploaded file is missing 'first name', 'middle name', 'surname', 'phone', or 'gender' column.");
            return redirect()->back();
        }

        $failures = $phonefailures = [];
        $index = 2;
        foreach ($rows as $row) {
            $gender = $row[$genderIndex] ?? null;
            $phone = $row[$phoneIndex] ?? null;

            if ($gender != 'Female' && $gender != 'Male') {
                $failures[] = $index;
            }

            if (!preg_match("/[0-9]{9}$/", $phone)){
                dd($index);
                 $phonefailures[] = $index;
            }
            $index++;
        }

        if (!empty($phonefailures)) {
            $count = count($phonefailures);
            
            if ($count === 1) {
                $rowList = $phonefailures[0];
            } else {
                $lastRow = array_pop($phonefailures);
                $rowList = implode(', ', $phonefailures);
                $rowList .= " and " . $lastRow;
            }
            
            $message = "Please enter phone number in 7XXXXXXXX format in row " . $rowList . '.';
            
            flash()->option('position', 'bottom-right')->error($message);
            return redirect()->back();
        }

        if (!empty($failures)) {
            $count = count($failures);
            
            if ($count === 1) {
                $rowList = $failures[0];
            } else {
                $lastRow = array_pop($failures);
                $rowList = implode(', ', $failures);
                $rowList .= " and " . $lastRow;
            }
            
            $message = "Please enter 'Male' or 'Female' in row " . $rowList . '.';
            
            flash()->option('position', 'bottom-right')->error($message);
            return redirect()->back();
        }

        $file->store('import');

        $import =Excel::import(new TeacherImport,  $file);

        flash()->option('position', 'bottom-right')->success('You have successfully uploaded the file.');
        return redirect()->route('teachers.index');
    }
    

    public function add()
    {
        $this->resetValidation();
        $this->validate();

        try{

            DB::beginTransaction();

            $generatedPassword = Str::random(8);
            
            $teacher = User::create([
                'name' =>$this->first_name .' '.($this->middle_name ?? ''). ' '.$this->sur_name,
                'email' => $this->email ? $this->email : null ,
                'gender' => $this->gender,
                'phone' => $this->phone,
                'username' => $this->phone,
                'password' => $generatedPassword, // Use bcrypt to hash the password
                'school_id' => auth()->user()->school->id,
                'is_verified' => true,
                'created_by' => auth()->user()->id
            ]);

            if($this->role){
                $teacher->assignRole($this->role);
            }else{
    
                $teacher->assignRole('teacher');
            }

            $smsMessage = "Hongera! Username yako ni {$teacher->username} na password ni {$generatedPassword}. Tafadhali tembelea www.shulemis.ac.tz ili kuanza.";

$response = Http::withToken(config('sms.SMS_API_KEY')) // API Key from .env
    ->acceptJson()
    ->post('https://sms.webline.co.tz/api/v3/sms/send', [
        'recipient' => $teacher->phone,
        'sender_id' => config('sms.SENDER_ID'), // e.g., TAARIFA
        'type'       => 'plain',
        'message'    => $smsMessage,
    ]);
    
            DB::commit();
            flash()->option('position','bottom-right')->success('A teacher has been succesfull added.');
    
            return redirect()->route('teachers.index');

        }catch(\Exception $e){
            DB::rollBack();
            flash()->option('position','bottom-right')->error('There was an error occured '. $e->getMessage());

        }
      
       
    }
}
