@component('mail::message')
# Welcome, {{ $teacher->name }}!

Your account has been created successfully.
Your login password is: **{{ $password }}**.

Please log in and change your password at your earliest convenience.

Thank you!

@component('mail::button', ['url' => url('/login')])
Log in to Your Account
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
