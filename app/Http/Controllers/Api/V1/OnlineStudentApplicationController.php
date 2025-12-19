<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnlineApplicationController extends Controller
{
    public function index(){
        return view("onlineApplication.online-application");
    }
}
