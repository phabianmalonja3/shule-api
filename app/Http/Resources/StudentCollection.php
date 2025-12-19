<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class StudentCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->transform(function ($student) {
                return [
                    'id' => $student->id,
                    'fullname' => $student->name,
                    'email' => $student->email,
                    'class' => $student->studentClass->class_name ?? null, // Assuming class_name is the field in school_classes table
                    'stream' => $student->stream->stream_name ?? null, // Assuming stream_name is the field in streams table
                ];
            }),
            'meta' => [
                'current_page' => $this->currentPage(),
                'last_page' => $this->lastPage(),
                'per_page' => $this->perPage(),
                'total' => $this->total(),
            ],
        
        ];
    }
    public function with(Request $request): array
    {
        return [

            'success' => true,
            'message' => 'ok',

        ];
    }
}
