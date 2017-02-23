<div class="nav_profile">
    <div class="media profile-left">
        <a href="#" class="pull-left profile-thumb">
                <img alt="User Image" class="img-circle"
                     src="{{ url($user->picture) }}">
        </a>
        <div class="content-profile">
            <h4 class="media-heading">{{ $user->full_name }}</h4>
            <span class="text-white">{{ trans('left_menu.human_resources') }}</span>
        </div>
    </div>
</div>
<ul class="navigation">
    <li {!! (Request::is('/') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/')}}">
            <i class="menu-icon fa fa-fw fa-dashboard text-primary"></i>
            <span class="mm-text ">{{ trans('secure.dashboard') }}</span>
        </a>
    </li>
    <li>
        <a target="_blank" href="{{url('manual/school-management-system.pdf')}}">
            <i class="menu-icon fa fa-clipboard text-default"></i>
            <span class="mm-text">{{ trans('secure.user_manual') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'parent') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/parent')}}">
            <i class="menu-icon fa fa-user-md text-info"></i>
            <span class="mm-text">{{ trans('left_menu.parents') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'teacher') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/teacher')}}">
            <i class="menu-icon fa fa-user-secret text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.teachers') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'librarian') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/librarian')}}">
            <i class="menu-icon fa fa-user text-primary"></i>
            <span class="mm-text">{{ trans('left_menu.librarians') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'staff_attendance') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/staff_attendance')}}">
            <i class="menu-icon fa fa-taxi text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.staff_attendance') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'salary') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/salary')}}">
            <i class="menu-icon fa fa-credit-card text-success"></i>
            <span class="mm-text">{{ trans('left_menu.salary') }}</span>
        </a>
    </li>
    <!--li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'feedback') ? 'class="active"  id="active"' : '') !!}>
        <a href="{{url('/feedback')}}">
            <i class="menu-icon fa fa-comment text-warning"></i>
            <span class="mm-text ">{{ trans('left_menu.feedback') }}</span>
        </a>
    </li-->
</ul>