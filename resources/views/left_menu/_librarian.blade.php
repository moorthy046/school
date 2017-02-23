<div class="nav_profile">
    <div class="media profile-left">
        <a href="#" class="pull-left profile-thumb">
                <img alt="User Image" class="img-circle"
                     src="{{ url($user->picture) }}">
        </a>
        <div class="content-profile">
            <h4 class="media-heading">{{ $user->full_name }}</h4>
            <span class="text-white">{{ trans('left_menu.librarian') }}</span>
        </div>
    </div>
</div>
<ul class="navigation">
    <li {!! (Request::is('/') ? 'class="active" id="active" id="active"' : '') !!}>
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
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'book') &&
            !starts_with(Route::getCurrentRoute()->getPath(), 'booklibrarian')? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/book')}}">
            <i class="menu-icon fa fa-book text-success"></i>
            <span class="mm-text">{{ trans('left_menu.books') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'booklibrarian') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/booklibrarian')}}">
            <i class="menu-icon fa fa-list text-info"></i>
            <span class="mm-text">{{ trans('left_menu.issue_books') }}</span>
        </a>
    </li>
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'reservedbook') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/reservedbook')}}">
            <i class="menu-icon fa fa-list-ol text-danger"></i>
            <span class="mm-text">{{ trans('left_menu.reserved_books') }}</span>
        </a>
    </li>
    <!--li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'dfeedbackiary') ? 'class="active"  id="active" id="active"' : '') !!}>
        <a href="{{url('/feedback')}}">
            <i class="menu-icon fa fa-comment text-warning"></i>
            <span class="mm-text ">{{ trans('left_menu.feedback') }}</span>
        </a>
    </li-->
</ul>