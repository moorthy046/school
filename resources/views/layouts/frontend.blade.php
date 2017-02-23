<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">

    <title> {{$title or 'SMS'}} | {{ Settings::get('name') }}</title>
    <link href="{{ asset('css/frontend.css') }}" rel="stylesheet" type="text/css"/>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar"
                        aria-expanded="false" aria-controls="navbar">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ url('uploads/site').'/'.Settings::get('logo') }}" alt="logo"
                         style="width:35px;height:35px;"/>
                    {{ Settings::get('name') }}
                </a>
            </div>
            <div id="navbar" class="navbar-collapse collapse">
                <ul class="nav navbar-nav">
                    @if(isset($pages))
                        @foreach ($pages as $item)
                            <li><a href="{{url('page/'.$item->id)}}">{{$item->title}}</a></li>
                        @endforeach
                    @endif
                    <li><a href="{{url('signin')}}">{{trans('auth.login')}}</a></li>
                </ul>
            </div>
        </div>
    </nav>
    @include('flash::message')
    @yield('content')
</div>
</body>
</html>