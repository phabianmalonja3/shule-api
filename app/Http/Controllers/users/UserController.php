<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserController extends Controller
{
    public function showUserManual(Request $request)
    {
        $path = 'manuals/user_manual.pdf'; // Path relative to storage/app

        if (Storage::exists($path)) {
            $file = Storage::get($path);
            $response = new StreamedResponse(function () use ($file) {
                echo $file;
            }, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="user_manual.pdf"', // 'inline' for viewing in browser
            ]);

            return $response;
        } else {
            abort(404); // Or return a view indicating the file is not found
        }
    }
}
