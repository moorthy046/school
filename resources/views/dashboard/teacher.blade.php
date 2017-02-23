@extends('layouts.secure')
@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-success col-md-12">
                <h5>{{trans('dashboard.teachergroups')}}</h5>
                <h2><i class="menu-icon fa fa-list-alt fa-1x"></i> <span id="teachergroups"></span></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-primary col-md-12">
                <h5>{{trans('dashboard.subjects')}}</h5>
                <h2><i class="menu-icon fa fa-list fa-1x"></i> <span id="subjects"></span></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-warning col-md-12">
                <h5>{{trans('dashboard.diaries')}}</h5>
                <h2><i class="menu-icon fa fa-comment fa-1x"></i> <span id="diaries"></span></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-danger col-md-12">
                <h5>{{trans('dashboard.exams')}}</h5>
                <h2><i class="menu-icon fa fa-file-excel-o fa-1x"></i> <span id="exams"></span> </h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <h1>{{trans('dashboard.calendar')}}</h1>
            <div id="calendar"></div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <h1>{{trans('dashboard.teachergroups')}}</h1>
            <ul class="list-group">
                @foreach($teachergroups->get() as $group)
                    <a href="{{url('teachergroup/'.$group->id.'/show')}}" class="list-group-item list-group-item-success">
                        {{$group->title}}
                    </a>
                @endforeach
            </ul>
        </div>
    </div>
@stop
@section('scripts')
    <script src="{{ asset('js/countUp.min.js') }}" type="text/javascript"></script>
    <script>
        $(function () {
            var useOnComplete = false,
                    useEasing = false,
                    useGrouping = false,
                    options = {
                        useEasing: useEasing, // toggle easing
                        useGrouping: useGrouping, // 1,000,000 vs 1000000
                        separator: ',', // character to use as a separator
                        decimal: '.' // character to use as a decimal
                    };
            var teachergroups = new CountUp("teachergroups", 0, "{{$teachergroups->count()}}", 0, 3, options);
            teachergroups.start();
            var subjects = new CountUp("subjects", 0, "{{$subjects}}", 0, 3, options);
            subjects.start();
            var diaries = new CountUp("diaries", 0, "{{$diaries}}", 0, 3, options);
            diaries.start();
            var exams = new CountUp("exams", 0, "{{$exams}}", 0, 3, options);
            exams.start();
        });
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                "header": {
                    "left": "prev,next today",
                    "center": "title",
                    "right": "month,agendaWeek,agendaDay"
                },
                "eventLimit": true,
                "firstDay": 1,
                "eventRender": function (event, element) {
                    element.popover({
                        content: event.description,
                        template: '<div class="popover" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content"></div></div>',
                        title: event.title,
                        container: 'body',
                        trigger: 'click',
                        placement: 'auto'
                    });
                },
                "eventSources": [
                    {
                        url: "{{url('events')}}",
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        error: function () {
                            alert('there was an error while fetching events!');
                        }
                    }
                ]
            });
        });
    </script>
@stop
