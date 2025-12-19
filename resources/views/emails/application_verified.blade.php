@component('mail::message')
# Dear {{ $user->fullname }},

Congratulations! Your school application has been **verified** successfully.

## Login Credentials:
- **Email:** {{ $user->email }}
- **Temporary Password:** {{ $defaultPassword }}

@component('mail::button', ['url' => url('login')])
Log in to your account
@endcomponent

For **security reasons**, we **highly recommend** that you change your password immediately after logging in.

If you have any questions, feel free to contact us.

Thanks,<br>
{{ config('app.name') }}
@endcomponent
