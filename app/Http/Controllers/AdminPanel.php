<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\School;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use App\Models\SchoolApplication;

class AdminPanel extends Controller
{


    public function index()
    {

        $applicationCount = SchoolApplication::count();
        $userCount = User::count();
        $schoolCount = School::count();
    return view("admin.home",compact("applicationCount","schoolCount","userCount"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
