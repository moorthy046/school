<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts._meta')
    @include('layouts._assets')
</head>
<body id="bg-img">
<div class="app" id="app">
    <!-- ############ LAYOUT START-->
    <div class="login animated fadeIn">
        <div class="navbar">
                <!-- brand -->
            <a class="navbar-brand text-center" style="float:none;">
                <img src="{{ asset('uploads/site/'.Settings::get('logo')) }}"
                     alt="{{ Settings::get('name') }}" class="img-responsive"
                     style="margin:auto;width:100px;height:35px;">
                </a>
                <!-- / brand -->
        </div>
        @include('flash::message')
        @yield('content')
    </div>

    <!-- ############ LAYOUT END-->

</div>
@include('layouts._assets_footer')
</body>
</html>
