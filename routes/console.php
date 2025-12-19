<?php
use App\Models\Announcement;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();


Schedule::call(function () {

    Announcement::where('is_active', true) 
    ->whereDate('end_date', '<', now()) 
    ->update(['is_active' => false]);
    // $sevenDaysAgo = now()->subDays(7);

    // // Delete applications with status 'progress' and older than 7 days
    // $progressDeletedCount = DB::table('school_applications')
    //     ->where('status', 'progress')
    //     ->where('created_at', '<', $sevenDaysAgo)
    //     ->delete();

    // // Delete applications with status 'complete' and older than 7 days
    // $completeDeletedCount = DB::table('school_applications')
    //     ->where('status', 'complete')
    //     ->where('created_at', '<', $sevenDaysAgo)
    //     ->delete();

    // Optional: Log how many applications were deleted (for monitoring)
    // \Log::info("Deleted $progressDeletedCount 'progress' applications and $completeDeletedCount 'complete' applications.");
})->everySecond();



