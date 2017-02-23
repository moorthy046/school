<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts._meta')
    @include('layouts._assets')
    @yield('styles')
</head>
<body>
<header class="header">
    <nav class="navbar navbar-static-top" role="navigation">
        <a href="{{ url('/') }}" class="logo">
            <img src="{{ url('uploads/site').'/'.Settings::get('logo') }}" alt="logo" style="width:35px;height:35px;"/>
            {{ Settings::get('name') }}
        </a>
        <div>
            <a href="#" class="navbar-btn sidebar-toggle" data-toggle="offcanvas" role="button"> <i
                        class="fa fa-fw fa-navicon"></i>
            </a>
        </div>
        <div class="navbar-right">
            <ul class="nav navbar-nav">
                @include("layouts._header-right")
            </ul>
        </div>
    </nav>
</header>
<div class="wrapper row-offcanvas row-offcanvas-left">
    <aside class="left-side sidebar-offcanvas">
        <section class="sidebar">
            <div id="menu" role="navigation">
                @if ($user->inRole('super_admin') && !$user->inRole('admin'))
                    @include('/left_menu._super_admin')
                @elseif ($user->inRole('admin') && !$user->inRole('super_admin'))
                    @include('/left_menu._admin')
                @elseif ($user->inRole('admin') && $user->inRole('super_admin'))
                    @include('/left_menu._admin_super_admin')
                @elseif ($user->inRole('human_resources'))
                    @include('/left_menu._human_resources')
                @elseif ($user->inRole('parent'))
                    @include('/left_menu._parent')
                @elseif ($user->inRole('student'))
                    @include('/left_menu._student')
                @elseif ($user->inRole('teacher'))
                    @include('/left_menu._teacher')
                @elseif ($user->inRole('librarian'))
                    @include('/left_menu._librarian')
                @else
                    @include('/left_menu._visitor')
                @endif
            </div>
        </section>
    </aside>
    <aside class="right-side right-padding">
        <div class="right-content">
            <h1>{{ $title or 'Welcome to SMS' }}</h1>
            @include('flash::message')
            @yield('content')
        </div>
    </aside>
</div>
@include('layouts._assets_footer')
@yield('scripts')
<script>
    $(document).ready(function () {
        $('.date').datetimepicker({
            format: '{{ Settings::get('jquery_date') }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down"
            }
        });
        $('.datetime').datetimepicker({
            format: '{{ Settings::get('jquery_date_time') }}',
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down"
            }
        });
        $('.date').on('change dp.change', function (e) {
            $('.date').trigger('change');
        })
        $('.datetime').on('change dp.change', function (e) {
            $('.datetime').trigger('change');
        })
    })
</script>
</body>
</html>