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
    <li class="menu-dropdown  {!! (starts_with(Route::getCurrentRoute()->getPath(), 'schoolyear')
                    || starts_with(Route::getCurrentRoute()->getPath(), 'semester')
                    || starts_with(Route::getCurrentRoute()->getPath(), 'direction')
                    || starts_with(Route::getCurrentRoute()->getPath(), 'subject')
                    || starts_with(Route::getCurrentRoute()->getPath(), 'marktype')
                    || starts_with(Route::getCurrentRoute()->getPath(), 'markvalue')
                    || starts_with(Route::getCurrentRoute()->getPath(), 'noticetype')
                    || starts_with(Route::getCurrentRoute()->getPath(), 'behavior') ? 'active' : '') !!}">
        <a href="{{url('#')}}">
            <i class="menu-icon fa fa-pencil text-info"></i>
            <span class="mm-text">{{ trans('left_menu.global_for_schools') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sub-menu">
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
        </ul>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'schools') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/schools')}}">
            <i class="menu-icon fa fa-server text-success"></i>
            <span class="mm-text">{{ trans('left_menu.schools') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'login_history') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/login_history')}}">
            <i class="menu-icon fa fa-sign-in text-info"></i>
            <span class="mm-text">{{ trans('left_menu.login_history') }}</span>
        </a>
    </li>
    <li {!! ((starts_with(Route::getCurrentRoute()->getPath(), 'notice') && !starts_with(Route::getCurrentRoute()->getPath(), 'noticetype'))? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/notice')}}">
            <i class="menu-icon fa fa-paper-plane text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.notice') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'diary') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/diary')}}">
            <i class="menu-icon fa fa-comment text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.diary') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'section') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/section')}}">
            <i class="menu-icon fa fa-graduation-cap text-success"></i>
            <span class="mm-text">{{ trans('left_menu.sections') }}</span>
        </a>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'student') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/student')}}">
            <i class="menu-icon fa fa-users text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.students') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'student_final_mark') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/student_final_mark')}}">
            <i class="menu-icon fa fa-list-ol text-success"></i>
            <span class="mm-text">{{ trans('left_menu.student_final_mark') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'parent') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/parent')}}">
            <i class="menu-icon fa fa-user-md text-info"></i>
            <span class="mm-text">{{ trans('left_menu.parents') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'human_resource') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/human_resource')}}">
            <i class="menu-icon fa fa-user-md text-success"></i>
            <span class="mm-text">{{ trans('left_menu.human_resource') }}</span>
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
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'visitors') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/visitor')}}">
            <i class="menu-icon fa fa-user text-success"></i>
            <span class="mm-text">{{ trans('left_menu.visitors') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'scholarship') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/scholarship')}}">
            <i class="menu-icon fa fa-gift text-info"></i>
            <span class="mm-text">{{ trans('left_menu.scholarship') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'salary') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/salary')}}">
            <i class="menu-icon fa fa-credit-card text-success"></i>
            <span class="mm-text">{{ trans('left_menu.salary') }}</span>
        </a>
    </li>
    @if(Settings::get('twilio_sid')!="")
        <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'sms_message') ? 'class="active" id="active"' : '') !!}>
            <a href="{{url('/sms_message')}}">
                <i class="menu-icon fa fa-envelope text-warning"></i>
                <span class="mm-text">{{ trans('left_menu.sms_message') }}</span>
            </a>
        </li>
    @endif
    <li class="menu-dropdown {!! (starts_with(Route::getCurrentRoute()->getPath(), 'dormitory') ? 'active' : '') !!}">
        <a href="{{url('#')}}">
            <i class="menu-icon fa fa-bed text-info"></i>
            <span class="mm-text">{{ trans('left_menu.dormitories') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sub-menu">
            <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'dormitory')  ? 'class="active"' : '') !!}>
                <a href="{{url('/dormitory')}}">
                    <i class="menu-icon fa fa-list text-warning"></i>
                    <span class="mm-text">{{ trans('left_menu.dormitories') }}</span>
                </a>
            </li>
            <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'dormitoryroom') ? 'class="active" id="active"' : '') !!}>
                <a href="{{url('/dormitoryroom')}}">
                    <i class="menu-icon fa fa-list-ol text-danger"></i>
                    <span class="mm-text">{{ trans('left_menu.dormitory_rooms') }}</span>
                </a>
            </li>
            <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'dormitorybed')  ? 'class="active" id="active"' : '') !!}>
                <a href="{{url('/dormitorybed')}}">
                    <i class="menu-icon fa  fa-bed text-success"></i>
                    <span class="mm-text">{{ trans('left_menu.dormitory_beds') }}</span>
                </a>
            </li>
        </ul>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'transportation') ? 'class="active"' : '') !!}>
        <a href="{{url('/transportation')}}">
            <i class="menu-icon fa fa-compass text-primary"></i>
            <span class="mm-text">{{ trans('left_menu.transportation') }}</span>
        </a>
    </li>
    <li class="menu-dropdown {!! (starts_with(Route::getCurrentRoute()->getPath(), 'invoice')
                        || starts_with(Route::getCurrentRoute()->getPath(), 'payment') ? 'active' : '') !!}">
        <a href="{{url('#')}}">
            <i class="menu-icon fa fa-list text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.payment') }}</span>
            <i class="fa fa-angle-left pull-right"></i>
        </a>
        <ul class="sub-menu">
            <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'invoice') ? 'class="active" id="active"' : '') !!}>
                <a href="{{url('/invoice')}}">
                    <i class="menu-icon fa fa-credit-card text-danger"></i>
                    <span class="mm-text">{{ trans('left_menu.invoice') }}</span>
                </a>
            </li>
            <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'payment') ? 'class="active" id="active"' : '') !!}>
                <a href="{{url('/payment')}}">
                    <i class="menu-icon fa fa-money text-success"></i>
                    <span class="mm-text">{{ trans('left_menu.payment') }}</span>
                </a>
            </li>
        </ul>
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