<?php

namespace App\Http\Controllers\Api\V1;
use OA\Get;
use OA\Response;
use App\Models\User;
use App\Models\School;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Rules\UniqueSchoolRule;
use PhpParser\Node\Stmt\ElseIf_;
use App\Models\SchoolApplication;
use Illuminate\Support\Facades\DB;
use App\Http\Resources\Application;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\ApplicationVerifiedMail;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Container\Attributes\Log;
use App\Notifications\NewschoolApplication;
use Illuminate\Support\Facades\Notification;
use App\Http\Resources\ApplicationCollection;
use App\Http\Resources\School as SchoolResourcr;
use Illuminate\Support\Facades\Log as FacadesLog;
use App\Http\Requests\UpdateSchoolApplicationRequest;
use App\Models\Combination;
use App\Models\GenericSchool;

class SchoolApplicationController extends Controller
{

    public function waiting()
    {
        return view('application.waiting');
    }

    public function showVerifyForm(SchoolApplication $application)
    {
        return view('application.verify', compact('application'));
    }


    public function scheduleApplication(SchoolApplication $application)
    {
        if ($application->status === 'pending') {
            $application->status = 'scheduled'; 
            $application->save(); 

            flash()->option('position','bottom-right')->success('Application has been scheduled.');
            return redirect()->route('application.list');
        }
        flash()->option('position','bottom-right')->error('Unable to schedule this application.');
        return redirect()->back();
    }
    public function index(Request $request)
    {
        $applications = SchoolApplication::latest()->where('status', '!=', 'complete')->paginate(10);

        return view('admin.application-list',compact('applications'));
    }

    public function updateStatus(SchoolApplication $application)
    {
        if ($application->status === 'pending') {
            $application->update(['status' => 'progress']);
        }

        flash()->option('position','bottom-right')->success('You Have recieved An application');

        return redirect()->back();
    }

    public function create(Request $request){
        return view('application.school-application');
    }

    public function store(Request $request)
    {   
        $registrationType = $request->input('registration_type', 'single');

        $schoolsRules = [
            'schools.*.school_name' => ['required', 'string', 'max:255'],
            'schools.*.address' => 'sometimes|max:255',
            'schools.*.school_type' => 'required|array|min:1|in:Primary,O-Level,A-Level',
            'schools.*.region' => 'required|string',
            'schools.*.district' => 'required|string',
            'schools.*.ward' => 'required|string',
            'schools.*.sponsorship_type' => 'required|in:Government,Private',
            'schools.*.first_name' => 'required|string',
            'schools.*.surname' => 'required|string',
            'schools.*.middle_name' => 'nullable|string',
            'schools.*.phone' => ['required', 'regex:/^0[0-9]{9}$/'],
            'schools.*.email' => ['nullable', 'email'],
        ];

    $validationRules = $schoolsRules;
    if ($registrationType === 'group') {
        $validationRules = array_merge($validationRules, ['generic_name' => 'required|string']);
    }

    $validatedData = $request->validate($validationRules, [
        'schools.*.phone.regex' => 'The phone number must start with 0 and contain exactly 10 digits.',
        'schools.*.phone.unique' => 'The phone number has already been used. Please make sure you are registering a new school or crosscheck the phone number.',
    ]);
        $schoolsData = $validatedData['schools'];
        
        $genericSchoolID = 1;
        $schoolCount = count($request->schools);

        if($registrationType === 'group' && $schoolCount > 1){

            $genericSchoolExists = GenericSchool::where('name', $request->generic_name)
                ->exists();

            if ($genericSchoolExists) {
                return redirect()->back()->withErrors([
                    'error' => "The schools' generic name has already been used. Please check with the Administrator."
                ]);
            }

            $genericSchool['name'] = $request->generic_name;
            $new = GenericSchool::create($genericSchool);
            $genericSchoolID = $new->id;
        }

        foreach ($schoolsData as $school) {

            if(in_array('Primary', $school['school_type']) && strpos(strtolower($school['school_name']),'primary') == 0 && strpos(strtolower($school['school_name']),'ya msingi') == 0){
                return redirect()->back()->withErrors([
                    'error' => $school['school_type']]);
            }

            $schoolExists = School::where('name', $school['school_name'])
                ->where('region', $school['region'])
                ->where('district', $school['district'])
                ->where('ward', $school['ward'])
                ->exists();

            if ($schoolExists) {
                return redirect()->back()->withErrors([
                    'error' => 'This school has already been registered in the ' . $school['region'] . ' ' . $school['district'] . ' ' . ' and' . ' ' . $school['ward']
                ]);
            }

            $headTeacherExists = SchoolApplication::where('phone',$school['phone'])->exists();
            
            if ($headTeacherExists) {
                return redirect()->back()->withErrors(['error' => 'User with phone number '.$school['phone'].' already exists in the system.']);
            }

            $location = [
                'ward' => $school['ward'],
                'region' => $school['region'],
                'district' => $school['district'],
            ];

            $school['status'] = 'pending';
            $school['school_name'] = ucwords(strtolower($school['school_name']));
            $school['school_type'] = json_encode($school['school_type']);
            $school['sponsorship_type'] = $school['sponsorship_type'];
            $school['location'] = json_encode($location);
            $school['fullname'] = $this->buildFullName($school);
            $school['generic_school_id'] = $genericSchoolID;

            SchoolApplication::create($school);
        }

        flash()->option('position', 'bottom-right')
            ->success('You have successfully submitted your application. We will contact you soon.');

        return redirect()->route('application.waiting');
    }

    public function show(SchoolApplication $application)
    {
        $school = School::where('name',  $application->school_name)->first();
        return view('application.application-show',compact('application','school'));
    }

    public function review()
    {
      return view("pending-approval");
    }


    private function buildFullName($data)
    {
        return $data['first_name'] . ' ' . ($data['middle_name'] ?? '') . ' ' . $data['surname'];
    }

    public function verifyApplication(Request $request, SchoolApplication $application)
    {
        try {
            $data = $request->validate([
                'registration_number' => 'required|unique:schools,registration_number',
                'motto' => 'required|string|max:255',
                'contract_number' => ['required','string','max:255','unique:schools,contract_number','regex:/^[A-Za-z0-9]+\/[A-Za-z0-9]+\/[A-Za-z0-9]+\/\d{4}\/\d{4}$/',
            ],

                'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{3}){1,2}$/', 'max:7'],
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
                'registration_number.unique' => 'The registration number is already in use.',
                'motto.required' => 'The motto field is required.',
                'color.required' => 'The color field is required.',
                'color.regex' => 'The color field must be a valid hex color code.',
                'color.max' => 'The color code should not exceed 7 characters.',
                'logo.image' => 'The logo must be an image file.',
                'logo.mimes' => 'The logo must be of type: jpeg, png, jpg, gif, svg.',
                'logo.max' => 'The logo file must not exceed 2MB.',
                'contract_number.regex' => 'The contract number must follow the format: anyword/anyword/anyword/0001/2024.',
            ]);
            
            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }
            
            $existingSchool = School::where('name', $application->school_name)
            ->where('region', $application->region)
            ->where('district', $application->district)
            ->where('ward', $application->ward)
            ->first();
            
            if ($existingSchool) {
                flash()->option('position', 'bottom-right')->error('A school with the same name already exists in the same region, ward, and district.');
                return back();
            }
            
            DB::beginTransaction();
            $application->is_verified = true;
            $application->status = 'complete';
            $application->save();
    
            $school = School::create([
                'name' => $application->school_name,
                'ward' => $application->ward,
                'district' => $application->district,
                'region' => $application->region,
                'city' => $application->city,
                'phone' => $application->phone,
                'postal_code' => $application->postal_code,
                'school_type' => json_encode(json_decode($application->school_type)),
                'location' => $application->location,
                'address' => $application->address,
                'sponsorship_type' => $application->sponsorship_type,
                'contract_number' => $data['contract_number'],
                'motto' => $data['motto'] ?? 'Default Motto',
                'logo' => $data['logo'] ?? null,
                'color' => $data['color'] ?? '#000000',
                'registration_number' => $data['registration_number'],
            ]);
    
            $primaryClasses = ['Class I', 'Class II', 'Class III', 'Class IV', 'Class V', 'Class VI'];
            $secondaryClasses = ['Form I', 'Form II', 'Form III', 'Form IV'];
            $alevelClasses = ['Form V', 'Form VI'];
    
            $primarySubjects = ['Kiswahili', 'Kiingereza', 'Hisabati', 'Maarifa ya Jamii', 'Sayansi na Teknolojia', 'Uraia na Maadili', 'Stadi za Kazi'];
            $olevelSubjects = ['Kiswahili', 'English', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'History', 'Geography', 'Civics'];
            $alevelSubjects = ['General Studies', 'Economics', 'Physics', 'Chemistry', 'Biology', 'Mathematics', 'Geography', 'History'];
    
            $schoolTypes = json_decode($application->school_type, true) ?? [];
            if (!is_array($schoolTypes)) {
                return back()->withErrors(['school_type' => 'Invalid school type data provided.']);
            }
            
            $classes = [];
            $subjects = [];

            foreach ($schoolTypes as $type) {
                if ($type === 'Primary') {
                    $classes = array_merge($classes, $primaryClasses);
                    $subjects = array_merge($subjects, $primarySubjects);
                } elseif ($type === 'O-Level') {
                    $classes = array_merge($classes, $secondaryClasses);
                    $subjects = array_merge($subjects, $olevelSubjects);
                } elseif ($type === 'A-Level') {
                    $classes = array_merge($classes, $alevelClasses);
                    $subjects = array_merge($subjects, $alevelSubjects);
                }
            }
            
            $school->combinations()->sync($combinations);

            preg_match('/\d{4}$/', $school->contract_number, $matches);

            $year = $matches[0];

            $academic_year =  AcademicYear::create([
                'school_id'=>$school->id,
                'year'=>$year,
                'is_active'=>true
            ]);


            $classes = array_unique($classes);
            $subjects = array_unique($subjects);
            
            foreach ($classes as $class) {
                $school->classes()->create(['name' => $class, 'created_by_system' => true,'academic_year_id'=>$academic_year->id]);
            }
            
            foreach ($subjects as $subject) {
                $school->subjects()->create(['name' => $subject, 'created_by_system' => true]);
            }
            
            $defaultPassword = 'password';
            $user = User::create([
                'name' => $application->fullname,
                'phone' => $application->phone,
                'username' => $application->phone,
                'password' =>$defaultPassword,
                'school_id' => $school->id,
                'is_verified' => true,
            ]);
    
            $user->assignRole('header teacher');
    
            DB::commit();
    
            flash()->option('position', 'bottom-right')->success('The application has been successfully verified.');
            return redirect()->route('school.list');
        } catch (\Exception $e) {
            DB::rollBack(); 
            flash()->option('position', 'bottom-right')->error('An error occurred during application verification: ' . $e->getMessage());
            return back();
        }
    }

}
