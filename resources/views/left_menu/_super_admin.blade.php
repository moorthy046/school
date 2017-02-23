<div class="nav_profile">
    <div class="media profile-left">
        <a href="#" class="pull-left profile-thumb">
            <img alt="User Image" class="img-circle"
                 src="{{ url($user->picture) }}">
        </a>
        <div class="content-profile">
            <h4 class="media-heading">{{ $user->full_name }}</h4>
            <span class="text-white">{{ trans('left_menu.super_admin') }}</span>
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
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'schoolyear') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/schoolyear')}}">
            <i class="menu-icon fa fa-briefcase text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.school_years') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'semester') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/semester')}}">
            <i class="menu-icon fa fa-list text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.semesters') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'direction') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/direction')}}">
            <i class="menu-icon fa fa-arrows-alt text-success"></i>
            <span class="mm-text">{{ trans('left_menu.directions') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'subject') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/subject')}}">
            <i class="menu-icon fa fa-list text-info"></i>
            <span class="mm-text">{{ trans('left_menu.subjects') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'marktype') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/marktype')}}">
            <i class="menu-icon fa fa-list-ul text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.mark_type') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'markvalue') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/markvalue')}}">
            <i class="menu-icon fa fa-list-ol text-success"></i>
            <span class="mm-text">{{ trans('left_menu.mark_value') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'noticetype') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/noticetype')}}">
            <i class="menu-icon fa fa-list-alt text-info"></i>
            <span class="mm-text">{{ trans('left_menu.notice_type') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'behavior') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/behavior')}}">
            <i class="menu-icon fa fa-indent text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.behavior') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'schools') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/schools')}}">
            <i class="menu-icon fa fa-server text-success"></i>
            <span class="mm-text">{{ trans('left_menu.schools') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'school_admin') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/school_admin')}}">
            <i class="menu-icon fa fa-user-md text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.school_admin') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'human_resource') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/human_resource')}}">
            <i class="menu-icon fa fa-user-md text-success"></i>
            <span class="mm-text">{{ trans('left_menu.human_resource') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'visitor') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/visitor')}}">
            <i class="menu-icon fa fa-user text-success"></i>
            <span class="mm-text">{{ trans('left_menu.visitors') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'login_history') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/login_history')}}">
            <i class="menu-icon fa fa-sign-in text-info"></i>
            <span class="mm-text">{{ trans('left_menu.login_history') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'pages') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/pages')}}">
            <i class="menu-icon fa fa-pagelines text-success"></i>
            <span class="mm-text">{{ trans('left_menu.page') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'certificate') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/certificate')}}">
            <i class="menu-icon fa fa-certificate text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.certificate') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'option') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/option')}}">
            <i class="menu-icon fa fa-cog text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.option') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'setting') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/setting')}}">
            <i class="menu-icon fa fa-cogs text-success"></i>
            <span class="mm-text">{{ trans('left_menu.settings') }}</span>
        </a>
    </li>
    <!--li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'feedback') ? 'class="active"  id="active"' : '') !!}>
        <a href="{{url('/feedback')}}">
            <i class="menu-icon fa fa-comment text-warning"></i>
            <span class="mm-text ">{{ trans('left_menu.feedback') }}</span>
        </a>
    </li-->
</ul>