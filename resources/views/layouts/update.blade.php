<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="shortcut icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <link rel="icon" href="{{ asset('img/favicon.ico') }}" type="image/x-icon">
    <title>{{trans('update.update')}} | SMS</title>
    @include('layouts._assets')
    @yield('styles')
    <link rel="stylesheet" href="{{ asset('css/install.css') }}">
</head>
<body>
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">
                SMS {{trans('update.update')}}
            </a>
        </div>
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                @include('update.steps')
            </ul>
        </div>
    </div>
</nav>
<div id="page-wrapper">
    <div class="container">
        @include('layouts.messages')
        @yield('content')
    </div>
</div>
<script src="{{ asset('js/libs.js') }}" type="text/javascript"></script>

@yield('scripts')
</body>
</html>
