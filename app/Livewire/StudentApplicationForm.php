<?php

namespace App\Livewire;

use App\Models\StudentApplication;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class StudentApplicationForm extends Component
{
    public string $exam_center = '';
    public string $exam_date = '';
    public string $language_preference = '';

    public string $first_name = '';
    public string $middle_name = '';
    public string $surname = '';
    public string $gender = '';
    public string $religion = '';
    public string $denomination = '';
    public string $residence = '';

    public array $family_contacts = [];
    public array $schools = [];

    public bool $submitted = false;

    public function mount(): void
    {
        $this->addFamilyContact();
        $this->addSchool();
    }

    public function addFamilyContact(): void
    {
        $nextKey = empty($this->family_contacts)
            ? 1
            : max(array_column($this->family_contacts, '_key')) + 1;

        $this->family_contacts[] = [
            '_key' => $nextKey,
            'relationship' => '',
            'first_name' => '',
            'middle_name' => '',
            'surname' => '',
            'phone' => '',
            'occupation' => '',
            'region' => '',
            'district' => '',
            'ward' => '',
            'street' => '',
        ];
    }

    public function removeFamilyContact(int $index): void
    {
        if (count($this->family_contacts) === 1) {
            return;
        }

        unset($this->family_contacts[$index]);
        $this->family_contacts = array_values($this->family_contacts);
        $this->resetValidation();
    }

    public function addSchool(): void
    {
        $nextKey = empty($this->schools)
            ? 1
            : max(array_column($this->schools, '_key')) + 1;

        $this->schools[] = [
            '_key' => $nextKey,
            'name' => '',
            'region' => '',
            'district' => '',
            'ward' => '',
            'street' => '',
            'max_standard' => '',
            'is_finalist' => false,
            'maths_avg_score' => '',
            'english_avg_score' => '',
            'science_avg_score' => '',
            'headteacher_name' => '',
            'headteacher_phone' => '',
        ];
    }

    public function removeSchool(int $index): void
    {
        if (count($this->schools) === 1) {
            return;
        }

        unset($this->schools[$index]);
        $this->schools = array_values($this->schools);
        $this->resetValidation();
    }

    public function submit(): void
    {
        $validated = $this->validate();

        DB::transaction(function () use ($validated): void {
            StudentApplication::create([
                'exam_center' => $validated['exam_center'],
                'exam_date' => $validated['exam_date'],
                'language_preference' => $validated['language_preference'],
                'applicant' => [
                    'first_name' => $validated['first_name'],
                    'middle_name' => $validated['middle_name'] ?: null,
                    'surname' => $validated['surname'],
                    'gender' => $validated['gender'],
                    'religion' => $validated['religion'],
                    'denomination' => $validated['denomination'] ?: null,
                    'residence' => $validated['residence'],
                ],
                'family_contacts' => collect($validated['family_contacts'])
                    ->map(fn (array $contact) => collect($contact)->except('_key')->all())
                    ->all(),
                'schools' => collect($validated['schools'])
                    ->map(fn (array $school) => collect($school)->except('_key')->all())
                    ->all(),
                'status' => 'submitted',
            ]);
        });

        $this->submitted = true;
        $this->dispatch('application-submitted');
    }

    public function startAnotherApplication(): void
    {
        $this->reset();
        $this->mount();
    }

    protected function rules(): array
    {
        $rules = [
            'exam_center' => ['required', Rule::in(array_keys($this->examCenters()))],
            'exam_date' => ['required', 'date', 'after_or_equal:today'],
            'language_preference' => ['required', Rule::in(['English', 'Kiswahili', 'No preference'])],
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'surname' => ['required', 'string', 'max:100'],
            'gender' => ['required', Rule::in(['Female', 'Male', 'Prefer not to say'])],
            'religion' => ['required', 'string', 'max:100'],
            'denomination' => ['nullable', 'string', 'max:100'],
            'residence' => ['required', Rule::in(['Father', 'Mother', 'Guardian'])],
            'family_contacts' => ['required', 'array', 'min:1'],
            'family_contacts.*._key' => ['required', 'integer'],
            'family_contacts.*.relationship' => ['required', Rule::in(['Father', 'Mother', 'Guardian', 'Other'])],
            'family_contacts.*.first_name' => ['required', 'string', 'max:100'],
            'family_contacts.*.middle_name' => ['nullable', 'string', 'max:100'],
            'family_contacts.*.surname' => ['required', 'string', 'max:100'],
            'family_contacts.*.phone' => ['required', 'string', 'max:30'],
            'family_contacts.*.occupation' => ['required', 'string', 'max:150'],
            'family_contacts.*.region' => ['required', 'string', 'max:100'],
            'family_contacts.*.district' => ['required', 'string', 'max:100'],
            'family_contacts.*.ward' => ['required', 'string', 'max:100'],
            'family_contacts.*.street' => ['required', 'string', 'max:150'],
            'schools' => ['required', 'array', 'min:1'],
            'schools.*._key' => ['required', 'integer'],
            'schools.*.name' => ['required', 'string', 'max:180'],
            'schools.*.region' => ['required', 'string', 'max:100'],
            'schools.*.district' => ['required', 'string', 'max:100'],
            'schools.*.ward' => ['required', 'string', 'max:100'],
            'schools.*.street' => ['required', 'string', 'max:150'],
            'schools.*.max_standard' => ['required', Rule::in(['Form IV', 'Form VI'])],
            'schools.*.is_finalist' => ['required', 'boolean'],
            'schools.*.maths_avg_score' => ['required', 'numeric', 'between:0,100'],
            'schools.*.english_avg_score' => ['required', 'numeric', 'between:0,100'],
            'schools.*.science_avg_score' => ['required', 'numeric', 'between:0,100'],
        ];

        foreach ($this->schools as $index => $school) {
            $requiredForFinalist = Rule::requiredIf((bool) ($school['is_finalist'] ?? false));
            $rules["schools.{$index}.headteacher_name"] = [$requiredForFinalist, 'nullable', 'string', 'max:150'];
            $rules["schools.{$index}.headteacher_phone"] = [$requiredForFinalist, 'nullable', 'string', 'max:30'];
        }

        return $rules;
    }

    public function examCenters(): array
    {
        return [
            'arusha' => 'Arusha City Centre',
            'dar-ilala' => 'Dar es Salaam — Ilala',
            'dodoma' => 'Dodoma City Centre',
            'mbeya' => 'Mbeya City Centre',
            'mwanza' => 'Mwanza — Nyamagana',
            'zanzibar' => 'Zanzibar — Urban West',
        ];
    }

    public function render()
    {
        return view('livewire.student-application-form');
    }
}
