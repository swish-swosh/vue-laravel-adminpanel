@component('mail::message')
# Thanks for signing up! Before getting started, please verify by clicking the verification button below:

Please click confirm to activate:

@component('mail::button', ['url' => $url])
Confirm signup
@endcomponent

You will be automatically redirected to the login page upon confirmation.

If you’re having trouble clicking the "Confirm signup" button, copy and paste the URL below into your web browser:

<br>
<a class="link-text-xs" href="{{$url}}">{{$url}}</a><br>

@endcomponent