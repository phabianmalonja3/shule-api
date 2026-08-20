<?php

namespace App\Livewire\Applications;

use App\Models\User;
use App\Models\School;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\AcademicYear;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\Combination;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ApplicationVerify extends Component
{

    use WithFileUploads;

    public $school_name,$registration_number,$motto,$contract_number,$color,$logo,$school,$application;


protected $rules =[
    'registration_number' => ['required','unique:schools,registration_number'],
    'motto' => 'required|string|max:255',
    'contract_number' => ['required','string','max:255','unique:schools,contract_number','regex:/^[A-Za-z0-9]+\/[A-Za-z0-9]+\/[A-Za-z0-9]+\/\d{4}\/\d{4}$/'],

    'color' => ['nullable', 'string', 'regex:/^#([A-Fa-f0-9]{3}){1,2}$/', 'max:7']
    // 'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',[
    //     'registration_number.unique' => 'The registration number is already in use.',
    //     'motto.required' => 'The motto field is required.',
    //     'color.required' => 'The color field is required.',
    //     'color.regex' => 'The color field must be a valid hex color code.',
    //     'color.max' => 'The color code should not exceed 7 characters.',
    //     'logo.image' => 'The logo must be an image file.',
    //     'logo.mimes' => 'The logo must be of type: jpeg, png, jpg, gif, svg.',
    //     'logo.max' => 'The logo file must not exceed 2MB.',
    //     'contract_number.regex' => 'The contract number must follow the format: anyword/anyword/anyword/0001/2024.',
    // ]
];



public function mount($application){
    $this->application = $application;
    $this->school_name = $application->school_name;
}


    


    public function render()
    {
        return view('livewire.applications.application-verify',['application'=>$this->application]);
    }


    public function verifyApplication()
    {
        $this->validate();

        $existingSchool = School::where('name', $this->application->school_name)
            ->where('region', $this->application->region)
            ->where('district', $this->application->district)
            ->where('ward', $this->application->ward)
            ->first();

        if ($existingSchool) {
            flash()->option('position', 'bottom-right')->error('A school with the same name already exists in this location.');
            return back();
        }

        $schoolTypes = is_string($this->application->school_type) 
            ? json_decode($this->application->school_type, true) 
            : $this->application->school_type;

        if (!is_array($schoolTypes)) {
            return back()->withErrors(['school_type' => 'Invalid school type data provided.']);
        }

        try {
    
            // Start the transaction
            

            
            // $existingSchool = School::where('name', $this->application->school_name)
            // ->where('region', $this->application->region)
            // ->where('district', $this->application->district)
            // ->where('ward', $this->application->ward)
            // ->first();
            
            // if ($existingSchool) {
            //     flash()->option('position', 'bottom-right')->error('A school with the same name already exists in the same region, ward, and district.');
            //     return back();
            // }
            
            DB::beginTransaction();

            if ($this->logo) {
                $data['logo'] = $this->logo->store('logos', 'public');
            }

            $this->application->update([
                'is_verified' => true,
                'status' => 'complete',
            ]);

            // $this->application->is_verified = true;
            // $this->application->status = 'complete';
            // $this->application->save();

            $school = School::create([
                'name' => $this->application->school_name,
                'ward' => $this->application->ward,
                'district' => $this->application->district,
                'region' => $this->application->region,
                'city' => $this->application->city,
                'phone' => $this->application->phone,
                'postal_code' => $this->application->postal_code,
                'school_type' => json_encode($schoolTypes),
                'location' => $this->application->location,
                'address' => $this->application->address,
                'sponsorship_type' => $this->application->sponsorship_type,
                'contract_number' => $this->contract_number,
                'motto' => $this->motto?? 'Default Motto',
                'logo' => $this->logo ?? null,
                'color' => $this->color ?? '#000000',
                'registration_number' => $this->registration_number,
                'generic_school_id' => $this->application->generic_school_id,
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
            
            // if($setCombination){
            //     $combinationIds = Combination::whereIn('id', [1,2])->pluck('id')->all();
            //     $pivotData = [
            //         'created_by' => Auth::id(),
            //         'created_at' => now(),
            //         'updated_at' => now()
            //     ];

            //     $combinationsToSync = [];
            //     foreach ($combinationIds as $id) {
            //         $combinationsToSync[$id] = $pivotData;
            //     }

            //     $school->combinations()->sync($combinationsToSync);
            // }

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
            
            $defaultPassword = '$2y$12$LABrQzvhXtKFDpsuxu5yC.ZPpaDEvSIrPMlxurINkMuVD63PGEXbW';
            $user = User::create([
                'name' => $this->application->fullname,
                'phone' => $this->application->phone,
                'username' => $this->application->phone,
                'password' =>$defaultPassword,
                'school_id' => $school->id,
                'is_verified' => true,
            ]);
    
            $user->assignRole('header teacher');
    
            DB::commit(); // Commit the transaction
            
            // $subjects  = $school->subjects(); 
            // $schoolCombinations = $school->combinations;

            // $generalSubjects    = ['English Language','Business Studies','Historia ya Tanzania na Maadili','Kiswahili','Basic Mathematics','Geography'];
            // $scienceSubjects    = ['Physics', 'Chemistry', 'Biology'];
            // $businessSubjects   = ['Book Keeping'];

            // foreach($schoolCombinations as $combination){
            //     if($combination->name == 'Science'){
            //         $scienceSubjects = array_merge($scienceSubjects, $generalSubjects);
            //         $combinationSubjectIDs = $subjects->whereIn('name',$scienceSubjects)->pluck('id')->toArray();

            //         $combination->subjects()->sync($combinationSubjectIDs);
            //     }elseif($combination->name == 'Business'){
            //         $businessSubjects = array_merge($businessSubjects, $generalSubjects);
            //         $combinationSubjectIDs = $subjects->whereIn('name',$businessSubjects)->pluck('id')->toArray();

            //         $combination->subjects()->sync($combinationSubjectIDs);                    
            //     }
            // }
			
            // $apiKey = config('sms.api_key');
            // $senderId = config('sms.sender_id');
            // $message = "Hongera kwa kujiunga na ShuleMIS. Username yako ni {$user->username} na password ni {$randomPassword}. 
            //             Tafadhali tembelea www.shulemis.ac.tz ili uanze kufurahia huduma zetu.";

            // Http::withToken($apiKey)
            //     ->acceptJson()
            //     ->post('https://sms.webline.co.tz/api/v3/sms/send', [
            //         'recipient' => $phoneNumber, // e.g. 255766031128
            //         'sender_id' => $senderId,     // e.g. TAARIFA
            //         'type'       => 'plain',
            //         'message'    => $message,
            //     ]);

            flash()->option('position', 'bottom-right')->success('The application has been successfully verified.');
            return redirect()->route('school.list');
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction on error
            flash()->option('position', 'bottom-right')->error('An error occurred during application verification: ' . $e->getMessage());
            return back();
        }
    }
}
