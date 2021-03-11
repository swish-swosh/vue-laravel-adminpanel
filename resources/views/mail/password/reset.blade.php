@component('mail::message')
# Forgot your Vue adminpanel password? No problem.

Please click 'Reset password' to confirm:

@component('mail::button', ['url' => $url])
Reset password
@endcomponent

If you did not request a password reset, it doesn't mean someone else has gained access. If unsure please update your email password where this request is send.

If you’re having trouble clicking the "Reset password" button, copy and paste the URL below into your web browser:

<br>
<a href="{{$url}}">{{$url}}</a><br>

@endcomponent