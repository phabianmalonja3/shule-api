<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class Teacher extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->id,
        "name"=> $this->name,
        "email"=>$this->email,
        "is_verified"=>(boolean)$this->is_verified,
        'school_name'=>$this->school->name,
        'role' => $this->getRoleNames()->first() ?? 'No Role Assigned',
        // 'class '=>$this->classTeacher->name ?? 'No class Assigned'
        // Safely get the first role name

        ];
    }
}
