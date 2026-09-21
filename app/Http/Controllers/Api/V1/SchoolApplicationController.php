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
        $combinations = Combination::get();
        return view('application.school-application',compact('combinations'));
    }

public function store(Request $request)
{
    $registrationType = $request->input('registration_type', 'single');

    $schoolsRules = [
        'schools.*.school_name' => ['required', 'string', 'max:255'],
        'schools.*.address' => ['nullable', 'string', 'max:255'],
        'schools.*.school_type' => ['required', 'array', 'min:1'],
        'schools.*.school_type.*' => ['in:Primary,O-Level,A-Level'],
        'schools.*.o_level_combinations' => ['nullable', 'array'],
        'schools.*.o_level_combinations.*' => ['integer'],
        'schools.*.a_level_combinations' => ['nullable', 'array'],
        'schools.*.a_level_combinations.*' => ['string'],
        'schools.*.region' => ['required', 'string'],
        'schools.*.district' => ['required', 'string'],
        'schools.*.ward' => ['required', 'string'],
        'schools.*.sponsorship_type' => ['required', 'in:Government,Private'],
        'schools.*.first_name' => ['required', 'string', 'max:100'],
        'schools.*.surname' => ['required', 'string', 'max:100'],
        'schools.*.middle_name' => ['nullable', 'string', 'max:100'],
        'schools.*.phone' => ['required', 'regex:/^0[0-9]{9}$/'],
        'schools.*.email' => ['nullable', 'email', 'max:255'],
    ];

    $validationRules = $schoolsRules;
    if ($registrationType === 'group') {
        $validationRules['generic_name'] = ['required', 'string', 'max:255'];
    }

    $validatedData = $request->validate($validationRules, [
        'schools.*.phone.regex' => 'The phone number must start with 0 and contain exactly 10 digits.',
        'schools.*.school_type.required' => 'Please select at least one school level.',
    ]);

    $schoolsData = $validatedData['schools'];
    $schoolCount = count($schoolsData);

    if ($registrationType === 'group' && $schoolCount > 1) {
        $genericSchoolExists = GenericSchool::where('name', $request->generic_name)->exists();
        if ($genericSchoolExists) {
            return redirect()->back()->withInput()->withErrors([
                'error' => "The generic school name '{$request->generic_name}' has already been registered."
            ]);
        }
    }

    foreach ($schoolsData as $index => $school) {
        $nameLower = strtolower($school['school_name']);
        $types = $school['school_type'];

        if (in_array('Primary', $types) && !Str::contains($nameLower, ['primary', 'msingi'])) {
            return redirect()->back()->withInput()->withErrors([
                'error' => "School #".($index + 1)." ({$school['school_name']}) is marked as Primary but its name does not contain 'Primary' or 'Msingi'."
            ]);
        }

        $schoolExists = School::where('name', $school['school_name'])
            ->where('region', $school['region'])
            ->where('district', $school['district'])
            ->where('ward', $school['ward'])
            ->exists();

        if ($schoolExists) {
            return redirect()->back()->withInput()->withErrors([
                'error' => "The school '{$school['school_name']}' is already registered in {$school['region']}, {$school['district']} ({$school['ward']})."
            ]);
        }

        $headTeacherExists = SchoolApplication::where('phone', $school['phone'])->exists();
        if ($headTeacherExists) {
            return redirect()->back()->withInput()->withErrors([
                'error' => "User with phone number {$school['phone']} already exists in the system."
            ]);
        }
    }

DB::transaction(function () use ($registrationType, $schoolCount, $request, $schoolsData) {
    $genericSchoolID = 1;

    if ($registrationType === 'group' && $schoolCount > 1) {
        $newGeneric = GenericSchool::create(['name' => $request->generic_name]);
        $genericSchoolID = $newGeneric->id;
    }

    foreach ($schoolsData as $school) {
        $location = [
            'ward' => $school['ward'],
            'region' => $school['region'],
            'district' => $school['district'],
        ];

        // Merge O-Level and A-Level selections into one flat array
        $oLevelCombs = $school['o_level_combinations'] ?? [];
        $aLevelCombs = $school['a_level_combinations'] ?? [];
        $mergedCombinations = array_values(array_merge($oLevelCombs, $aLevelCombs));

        SchoolApplication::create([
            'generic_school_id' => $genericSchoolID,
            'school_name' => ucwords(strtolower($school['school_name'])),
            'address' => $school['address'] ?? null,
            'school_type' => $school['school_type'],
            'sponsorship_type' => $school['sponsorship_type'],
            'combinations' => $mergedCombinations, // Saved as a flat JSON array
            'location' => $location,
            'first_name' => $school['first_name'],
            'middle_name' => $school['middle_name'] ?? null,
            'surname' => $school['surname'],
            'fullname' => $this->buildFullName($school),
            'phone' => $school['phone'],
            'email' => $school['email'] ?? null,
            'status' => 'pending',
        ]);
    }
});
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

        $existingSchool = School::where('name', $application->school_name)
            ->where('region', $application->region)
            ->where('district', $application->district)
            ->where('ward', $application->ward)
            ->first();

        if ($existingSchool) {
            flash()->option('position', 'bottom-right')->error('A school with the same name already exists in this location.');
            return back();
        }

        $schoolTypes = is_string($application->school_type) 
            ? json_decode($application->school_type, true) 
            : $application->school_type;

        if (!is_array($schoolTypes)) {
            return back()->withErrors(['school_type' => 'Invalid school type data provided.']);
        }

         $combinations = is_string($application->combinations) 
            ? json_decode($application->combinations, true) 
            : $application->combinations;

        try {
            DB::beginTransaction();

            if ($request->hasFile('logo')) {
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }
            
            // $existingSchool = School::where('name', $application->school_name)
            // ->where('region', $application->region)
            // ->where('district', $application->district)
            // ->where('ward', $application->ward)
            // ->first();
            
            // if ($existingSchool) {
            //     flash()->option('position', 'bottom-right')->error('A school with the same name already exists in the same region, ward, and district.');
            //     return back();
            // }
            
            //$schoolTypes = is_string($application->school_type) ? json_decode($application->school_type, true) : $application->school_type;

            // $application->is_verified = true;
            // $application->status = 'complete';
            // $application->save();

            $application->update([
                'is_verified' => true,
                'status' => 'complete',
            ]);

            $school = School::create([
                'name' => $application->school_name,
                'ward' => $application->ward,
                'district' => $application->district,
                'region' => $application->region,
                'city' => $application->city,
                'phone' => $application->phone,
                'postal_code' => $application->postal_code,
                'school_type' => $schoolTypes,
                'location' => $application->location,
                'address' => $application->address,
                'sponsorship_type' => $application->sponsorship_type,
                'combinations' => $combinations,
                'contract_number' => $data['contract_number'],
                'motto' => $data['motto'] ?? 'Default Motto',
                'logo' => $data['logo'] ?? null,
                'color' => $data['color'] ?? '#000000',
                'registration_number' => $data['registration_number'],
            ]);
    
            $typeMappings = [
                'Primary' => [
                    'classes' => ['Class I', 'Class II', 'Class III', 'Class IV', 'Class V', 'Class VI'],
                    'subjects' => ['Kiswahili', 'Kiingereza', 'Hisabati', 'Maarifa ya Jamii', 'Sayansi na Teknolojia', 'Uraia na Maadili', 'Stadi za Kazi'],
                ],
                'O-Level' => [
                    'classes' => ['Form I', 'Form II', 'Form III', 'Form IV'],
                    'subjects' => ['Kiswahili', 'English', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'History', 'Geography', 'Civics'],
                ],
                'A-Level' => [
                    'classes' => ['Form V', 'Form VI'],
                    'subjects' => ['General Studies', 'Economics', 'Physics', 'Chemistry', 'Biology', 'Mathematics', 'Geography', 'History'],
                ],
            ];

            $classes = [];
            $subjects = [];

            foreach ($schoolTypes as $type) {
                if (isset($typeMappings[$type])) {
                    $classes = array_merge($classes, $typeMappings[$type]['classes']);
                    $subjects = array_merge($subjects, $typeMappings[$type]['subjects']);
                }
            }

            // $primaryClasses = ['Class I', 'Class II', 'Class III', 'Class IV', 'Class V', 'Class VI'];
            // $secondaryClasses = ['Form I', 'Form II', 'Form III', 'Form IV'];
            // $alevelClasses = ['Form V', 'Form VI'];
    
            // $primarySubjects = ['Kiswahili', 'Kiingereza', 'Hisabati', 'Maarifa ya Jamii', 'Sayansi na Teknolojia', 'Uraia na Maadili', 'Stadi za Kazi'];
            // $olevelSubjects = ['Kiswahili', 'English', 'Mathematics', 'Physics', 'Chemistry', 'Biology', 'History', 'Geography', 'Civics'];
            // $alevelSubjects = ['General Studies', 'Economics', 'Physics', 'Chemistry', 'Biology', 'Mathematics', 'Geography', 'History'];
    
            // $schoolTypes = json_decode($application->school_type, true) ?? [];
            // if (!is_array($schoolTypes)) {
            //     return back()->withErrors(['school_type' => 'Invalid school type data provided.']);
            // }
            
            // $classes = [];
            // $subjects = [];

            // foreach ($schoolTypes as $type) {
            //     if ($type === 'Primary') {
            //         $classes = array_merge($classes, $primaryClasses);
            //         $subjects = array_merge($subjects, $primarySubjects);
            //     } elseif ($type === 'O-Level') {
            //         $classes = array_merge($classes, $secondaryClasses);
            //         $subjects = array_merge($subjects, $olevelSubjects);
            //     } elseif ($type === 'A-Level') {
            //         $classes = array_merge($classes, $alevelClasses);
            //         $subjects = array_merge($subjects, $alevelSubjects);
            //     }
            // }
            
            // $school->combinations()->sync($combinations);

            preg_match('/\d{4}$/', $school->contract_number, $matches);
            $year = $matches[0] ?? date('Y');

            $academicYear = AcademicYear::create([
                'school_id' => $school->id,
                'year' => $year,
                'is_active' => true,
            ]);

            foreach (array_unique($classes) as $class) {
                $school->classes()->create([
                    'name' => $class,
                    'created_by_system' => true,
                    'academic_year_id' => $academicYear->id,
                ]);
            }

            // preg_match('/\d{4}$/', $school->contract_number, $matches);

            // $year = $matches[0];

            // $academic_year =  AcademicYear::create([
            //     'school_id'=>$school->id,
            //     'year'=>$year,
            //     'is_active'=>true
            // ]);


            // $classes = array_unique($classes);
            // $subjects = array_unique($subjects);
            
            // foreach ($classes as $class) {
            //     $school->classes()->create(['name' => $class, 'created_by_system' => true,'academic_year_id'=>$academic_year->id]);
            // }
            
            // foreach ($subjects as $subject) {
            //     $school->subjects()->create(['name' => $subject, 'created_by_system' => true]);
            // }
            
            $defaultPassword = '$2y$12$LABrQzvhXtKFDpsuxu5yC.ZPpaDEvSIrPMlxurINkMuVD63PGEXbW';
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

    public function getRegions()
    {
        $regions = DB::table('regions')->select('id', 'name')->orderBy('name', 'asc')->get()->map(function ($region) {
            $region->name = ucwords(strtolower($region->name)); 
            return $region;
        });
        return response()->json(['regions' => $regions]);
    }

    public function getDistricts(Request $request)
    {
        $regionId = $request->query('region_id');

        $districts = DB::table('districts')
            ->select('id', 'name')
            ->where('region_id', $regionId)
            ->orderBy('name', 'asc')
            ->get()->map(function ($district) {
                $district->name = ucwords(strtolower($district->name)); 
                return $district;
        });

        return response()->json(['districts' => $districts]);
    }

    public function getWards(Request $request)
    {
        $districtId = $request->query('district_id');

        $wards = DB::table('wards')
            ->select('id', 'name')
            ->where('district_id', $districtId)
            ->orderBy('name', 'asc')
            ->get()->map(function ($ward) {
                $ward->name = ucwords(strtolower($ward->name)); 
                return $ward;
        });;

        return response()->json(['wards' => $wards]);
    }
}
