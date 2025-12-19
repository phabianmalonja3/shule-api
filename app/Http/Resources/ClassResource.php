<?php

namespace App\Http\Resources;

use App\Http\Resources\User;
use Illuminate\Http\Request;
use App\Models\User as ModelsUser;
use Illuminate\Http\Resources\Json\JsonResource;

class ClassResource extends JsonResource
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
            "class teacher"=> new User(ModelsUser::find($this->class_teacher_id)) ?? 'No teacher',
            "streams"=> StreamResource::collection($this->streams),
        ];
    }
}
