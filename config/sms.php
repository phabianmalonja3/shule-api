<?php

return [

    /*
    |--------------------------------------------------------------------------
    | SMS Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for the SMS service.
    | This includes API credentials, sender ID, and other options.
    |
    */

    'username' => env('SMS_USERNAME', 'your_default_username'),
    'api_key' => env('SMS_API_KEY', 'your_default_api_key'),
    'sender_id' => env('SENDER_ID', 'YourSenderID'),

    // Additional settings can be added here as needed

];