@component('mail::message')
# One last step

We just need you to confirm your email address to confirm that you're human.

@component('mail::button', ['url' => route('register.confirm', ['token' => $user->confirmation_token])])
Confirm Email
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
