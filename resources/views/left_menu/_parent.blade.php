<div class="nav_profile">
    <div class="media profile-left">
        <a href="#" class="pull-left profile-thumb">
                <img alt="User Image" class="img-circle"
                     src="{{ url($user->picture) }}">
        </a>
        <div class="content-profile">
            <h4 class="media-heading">{{ $user->full_name }}</h4>
            <span class="text-white">{{ trans('left_menu.parent') }}</span>
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
            <span class="mm-text ">{{ trans('secure.user_manual') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'studentsection/timetable')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/studentsection/timetable')}}">
            <i class="menu-icon fa fa-calendar text-info"></i>
            <span class="mm-text">{{ trans('left_menu.timetable') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'bookuser/index')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/bookuser/index')}}">
            <i class="menu-icon fa fa-book text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.books') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'borrowedbook/index')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/borrowedbook/index')}}">
            <i class="menu-icon fa fa-list text-success"></i>
            <span class="mm-text">{{ trans('left_menu.borrowed_books') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'applyingleave')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/applyingleave')}}">
            <i class="menu-icon fa fa-external-link text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.applying_leave') }}</span>
        </a>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'notice')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/report/'.Session::get('current_student_user_id').'/notice')}}">
            <i class="menu-icon fa fa-paper-plane text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.notice') }}</span>
        </a>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'attendances')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/report/'.Session::get('current_student_user_id').'/attendances')}}">
            <i class="menu-icon fa fa-exchange text-info"></i>
            <span class="mm-text">{{ trans('left_menu.attendances') }}</span>
        </a>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'marks')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/report/'.Session::get('current_student_user_id').'/marks')}}">
            <i class="menu-icon fa fa-list-ol text-primary"></i>
            <span class="mm-text">{{ trans('left_menu.marks') }}</span>
        </a>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'exams')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/report/'.Session::get('current_student_user_id').'/exams')}}">
            <i class="menu-icon fa fa-file-excel-o text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.exams') }}</span>
        </a>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'subjectboo') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/report/'.Session::get('current_student_user_id').'/subjectbook')}}">
            <i class="menu-icon fa fa-list-alt text-info"></i>
            <span class="mm-text">{{ trans('left_menu.subjectbook') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'diary')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/diary')}}">
            <i class="menu-icon fa fa-comment text-warning"></i>
            <span class="mm-text">{{ trans('left_menu.diary') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'studentsection/payment')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/studentsection/payment')}}">
            <i class="menu-icon fa fa-money text-success"></i>
            <span class="mm-text">{{ trans('left_menu.payments') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'studentsection/invoice') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/studentsection/invoice')}}">
            <i class="menu-icon fa fa-credit-card text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.invoice') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'transportparent')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/transportparent')}}">
            <i class="menu-icon fa fa-compass text-primary"></i>
            <span class="mm-text">{{ trans('left_menu.transportation') }}</span>
        </a>
    </li>
    <li {!! (ends_with(Route::getCurrentRoute()->getPath(), 'forstudent')  ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/report/'.Session::get('current_student_user_id').'/forstudent')}}">
            <i class="menu-icon fa fa-flag-checkered text-info"></i>
            <span class="mm-text">{{ trans('left_menu.reports') }}</span>
        </a>
    </li>
    <!--li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'feedback')  ? 'class="active"  id="active" id="active"' : '') !!}>
        <a href="{{url('/feedback')}}">
            <i class="menu-icon fa fa-comment text-warning"></i>
            <span class="mm-text ">{{ trans('left_menu.feedback') }}</span>
        </a>
    </li-->
</ul>