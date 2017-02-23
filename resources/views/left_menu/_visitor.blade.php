<div class="nav_profile">
    <div class="media profile-left">
        <a href="#" class="pull-left profile-thumb">
                <img alt="User Image" class="img-circle"
                     src="{{ url($user->picture) }}">
        </a>
        <div class="content-profile">
            <h4 class="media-heading">{{ $user->full_name }}</h4>
            <span class="text-white">{{ trans('left_menu.visitor') }}</span>
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
    <li {!! (starts_with(Route::getCurrentRoute()->getPath(), 'visitor_card') ? 'class="active" id="active"' : '') !!}>
        <a href="{{url('/visitor_card/'.$user->id)}}" target="_blank">
            <i class="menu-icon fa fa-credit-card text-info"></i>
            <span class="mm-text">{{ trans('left_menu.visitor_card') }}</span>
        </a>
    </li>
</ul>