<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Str;
use AfricasTalking\SDK\AfricasTalking;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Validators\Failure;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class TeacherImport implements ToModel,WithValidation,WithHeadingRow
{

    use Importable;

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
       $user = new User([
            'name'        => $row['first_name'] . ' ' . ($row['middle_name'] ?? '') . ' ' . $row['surname'],
            'phone'       => '0'.$row['phone'],
            'username'    => '0'.$row['phone'],
            'gender'      => $row['gender'],
            'school_id'   => auth()->user()->school_id,
            'password'    => 'password', // Hash the password
            'is_verified' => true,
            'created_by' => auth()->user()->id
        ]);

        $user-> assignRole('teacher');
        $randomPassword = Str::random(6);
        $phoneNumber = $user->phone;

        if (substr($phoneNumber, 0, 1) === '0') {
            $phoneNumber = '+255' . substr($phoneNumber, 1);
            $username = 'phabianmalonja';
            $apiKey   = 'atsk_008f794596ae78dc0dbdb2e7fac8549283e480ecf30062c3d678157c9042055ee5e77f4f';
            
            $AT = new AfricasTalking($username, $apiKey);
            $sms = $AT->sms();
            
            // Format the message
            $message = "Hongera kwa kujiunga na ShuleMIS. Username yako ni {$user->username} na password ni {$randomPassword}. Tafadhali tembelea www.shulemis.ac.tz ili uanze kufurahia huduma zetu.";
                
                
            $sms->send([
                    'to'      => $phoneNumber, // Use the formatted number
                    'message' => $message
                ]);

            return $user;
        }
    }
    
    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:50',
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:50',
            ],
            'surname' => [
                'required',
                'string',
                'max:50',
            ],
            'phone' => [
                'required',
                'unique:users,phone'
            ],
            'gender' => [
                'required'
                 // Restrict gender to specific values
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'phone.required' => 'The Phone number in row :index is required',
            'phone.unique' => 'The phone number in row :index has already been taken.',
        ];
    }

    public function onFailure(Failure ...$failures)
    {
        
    }
}
