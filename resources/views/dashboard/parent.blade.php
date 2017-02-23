@extends('layouts.secure')
@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-success col-md-12">
                <h5>{{trans('dashboard.borrowed_books')}}</h5>
                <h2><i class="menu-icon fa fa-list fa-1x"></i> <span id="borrowed_books"></span></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-primary col-md-12">
                <h5>{{trans('dashboard.subjects')}}</h5>
                <h2><i class="menu-icon fa fa-comment fa-1x"></i> <span id="dairies"></span></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-warning col-md-12">
                <h5>{{trans('dashboard.attendances')}}</h5>
                <h2><i class="menu-icon fa fa-exchange fa-1x"></i> <span id="attendances"></span></h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-danger col-md-12">
                <h5>{{trans('dashboard.marks')}}</h5>
                <h2><i class="menu-icon fa fa-list-ol fa-1x"></i> <span id="marks"></span></h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <h1>{{trans('dashboard.calendar')}}</h1>
            <div id="calendar"></div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <div class="row">
                <h1>{{trans('dashboard.last_attendances')}}</h1>
                <ul class="list-group">
                    @foreach($attendances as $index => $item)
                        @if($index < 10)
                            <li class="list-group-item">
                                {{$item->date}} - ({{$item->hour}})
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
            <div class="row">
                <h1>{{trans('dashboard.last_marks')}}</h1>
                <ul class="list-group">
                    @foreach($marks as $index => $item)
                        @if($index < 10)
                            <li class="list-group-item">
                                {{$item->date}} - {{$item->mark_type}} ({{$item->mark_value}})
                            </li>
                        @endif
                    @endforeach
                </ul>
            </div>
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
            var borrowed_books = new CountUp("borrowed_books", 0, "{{$borrowed_books}}", 0, 3, options);
            borrowed_books.start();
            var dairies = new CountUp("dairies", 0, "{{$dairies}}", 0, 3, options);
            dairies.start();
            var attendances = new CountUp("attendances", 0, "{{$attendances->count()}}", 0, 3, options);
            attendances.start();
            var marks = new CountUp("marks", 0, "{{$marks->count()}}", 0, 3, options);
            marks.start();
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
