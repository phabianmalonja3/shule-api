<?php

namespace App\Rules;

use Closure;
use App\Models\School;
use Illuminate\Contracts\Validation\ValidationRule;

class UniqueSchoolRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $data = request()->all();

        // Ensure region, district, and ward are provided
        if (!isset($data['region'], $data['district'], $data['ward'])) {
            $fail('Region, district, and ward must be provided to validate the school uniqueness.');
            return;
        }

        // Check if a school with the same name and location exists
        $exists = School::where('name', $value)
            ->where('region', $data['region'])
            ->where('district', $data['district'])
            ->where('ward', $data['ward'])
            ->exists();

        if ($exists) {
            $fail('This school already exists in the specified region, district, and ward.');
        }

    }
}
