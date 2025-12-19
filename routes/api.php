<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\MarksUploadController;


Route::resource('/marks-upload',MarksUploadController::class);
