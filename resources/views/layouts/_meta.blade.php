<meta charset="UTF-8">
<title>
    {{$title or 'SMS'}} | {{ Settings::get('name') }}
</title>
<meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

<link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
<link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">

<meta id="token" name="token" value="{{ csrf_token() }}">
@if(Sentinel::check())
    <meta id="pusherKey" name="pusherKey" value="{{ Settings::get('pusher_key') }}">
    <meta id="userId" name="userId" value="{{ $user->id }}">
@endif