<li class="dropdown messages-menu">
    <mail-notifications url="{{ url('/') }}"></mail-notifications>
</li>
@if(isset($schools) && !($user->inRole('super_admin')))
    <li class="dropdown tasks-menu hidden-xs">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-fw fa-university black"></i>
            <span class="label label-danger text-center">{{$current_school_item}}</span>
        </a>
        <ul class="dropdown-menu dropdown-messages">
            <li class="dropdown-title">{{trans('secure.school')}}</li>
            @foreach($schools as $item)
                <li>
                    <a href="{{url('/setschool/'.$item->id)}}">
                        <i class="menu-icon fa fa-university
                            {{ (($item->id==$current_school)?"text-success": "text-warning") }}">
                        </i>
                        {{ $item->title }}
                    </a>
                </li>
            @endforeach
            <li class="dropdown-footer">
                <a href="{{url('/schools')}}">{{trans('secure.view_all')}}</a>
            </li>
        </ul>
    </li>
@endif
@if(isset($current_student_section))
    <li class="dropdown tasks-menu hidden-xs">
        <a href="#">
            <i class="fa fa-fw fa-graduation-cap black"></i>
            <span class="label label-primary text-center">{{$current_student_section}}</span>
        </a>
    </li>
@endif
@if(isset($student_groups))
    <li class="dropdown tasks-menu hidden-xs">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-fw fa-edit black"></i>
            <span class="label label-primary text-center">{{$current_student_group}}</span>
        </a>
        <ul class="dropdown-menu dropdown-messages">
            <li class="dropdown-title">{{trans('secure.student_group')}}</li>
            @foreach($student_groups as $group)
                <li>
                    <a href="{{url('/setgroup/'.$group->id)}}">
                        <i class="menu-icon fa fa-users
                            {{ (($group->id==$current_student_group_id)?"text-success": "text-warning") }}">
                        </i>
                        {{ $group->title }}
                    </a>
                </li>
            @endforeach
            <li class="dropdown-footer">
                <a href="{{url('/teachergroup')}}">{{trans('secure.view_all')}}</a>
            </li>
        </ul>
    </li>
@endif
@if(isset($student_ids))
    <li class="dropdown tasks-menu hidden-xs">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-fw fa-graduation-cap black1"></i>
            <span class="label label-warning text-center">{{$current_student_name}}</span>
        </a>
        <ul class="dropdown-menu dropdown-messages">
            <li class="dropdown-title">{{trans('secure.student_name')}}</li>
            @foreach($student_ids as $student)
                <li>
                    <a href="{{url('/setstudent/'.$student->id)}}">
                        <i class="menu-icon fa fa-users
                           {{ (($student->id==$current_student_id)?"text-success": "text-warning") }}">
                        </i>
                        {{ $student->name }}
                    </a>
                </li>
            @endforeach
            <li class="dropdown-footer">
                <a href="{{url('/parentsection')}}">{{trans('secure.view_all')}}</a></li>
            </li>
        </ul>
    </li>
@endif
@if(isset($school_years))
    <li class="dropdown tasks-menu">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-calendar black1"></i>
            <span class="label label-warning text-center">{{$current_school_year}}</span>
        </a>
        <ul class="dropdown-menu dropdown-messages">
            <li class="dropdown-title">{{trans('secure.school_year')}}</li>
            @foreach($school_years as $year)
                <li>
                    <a href="{{url('/setyear/'.$year->id)}}">
                        <i class="menu-icon fa fa-calendar
                           {{ (($year->id==$current_school_year_id)?"text-success": "text-warning") }}">
                        </i>
                        {{ $year->title }}
                    </a>
                </li>
            @endforeach
            <li class="dropdown-footer">
                <a href="{{url('/schoolyear')}}">{{trans('secure.view_all')}}</a>
            </li>
        </ul>
    </li>
@endif
<li class="dropdown user-menu">
    <a href="#" class="dropdown-toggle padding-user" data-toggle="dropdown">
        <img src="{{ url($user->picture) }}" width="35"
             class="img-circle img-responsive pull-left" height="35"
             alt="{{ $user->full_name }}">
        <div class="riot">
            <div>
                {{$user->full_name}}
                <span>
                    <i class="caret"></i>
                </span>
            </div>
        </div>
    </a>
    <ul class="dropdown-menu">
        <li class="user-header">
            <img src="{!! url($user->picture) !!}" alt="img"
                 class="img-circle img-bor"/>
            <p>{{ $user->full_name }}</p>
        </li>
        <li class="pad-3">
            <a href="{{ url('/profile') }}">
                <i class="fa fa-fw fa-user"></i>
                {{trans('auth.my_profile')}}
            </a>
            <a href="{{ url('/my_certificate') }}">
                <i class="fa fa-fw fa-certificate"></i>
                {{trans('auth.my_certificate')}}
            </a>
        </li>
        <li role="presentation"></li>
        <li role="presentation" class="divider"></li>
        <li class="user-footer">
            <div class="pull-right">
                <a href="{{ url('logout') }}" class="text-danger">
                    <i class="fa fa-fw fa-sign-out"></i>
                    {{trans('auth.logout')}}
                </a>
            </div>
        </li>
    </ul>
</li>