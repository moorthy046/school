@extends('layouts.secure')
@section('content')
    <div class="row">
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-success col-md-12">
                <h5>{{trans('dashboard.schools')}}</h5>
                <h2><i class="menu-icon fa fa-server fa-1x"></i>
                    <span id="schools"></span>
                </h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-primary col-md-12">
                <h5>{{trans('dashboard.teachers')}}</h5>
                <h2><i class="menu-icon fa fa-user-md fa-1x"></i>
                    <span id="teachers"></span>
                </h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-warning col-md-12">
                <h5>{{trans('dashboard.parents')}}</h5>
                <h2><i class="menu-icon fa fa-user fa-1x"></i>
                    <span id="parents"></span>
                </h2>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 col-xs-12">
            <div class="bg-danger col-md-12">
                <h5>{{trans('dashboard.directions')}}</h5>
                <h2><i class="menu-icon fa fa-arrows-alt fa-1x"></i>
                    <span id="directions"></span>
                </h2>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-xs-12">
            <h1>{{trans('dashboard.calendar')}}</h1>
            <div id="calendar"></div>
        </div>
        <div class="col-sm-6 col-xs-12">
            <h1>{{trans('dashboard.salary_by_month')}}</h1>
            <div id="salary_by_month"></div>
        </div>
    </div>
@stop
@section('scripts')
    <link rel="stylesheet" href="{{ asset('css/c3.min.css') }}">
    <script type="text/javascript" src="{{ asset('js/d3.v3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/d3.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/c3.min.js')}}"></script>
    <script src="{{ asset('js/countUp.min.js') }}" type="text/javascript"></script>
    <script>
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
            var schools = new CountUp("schools", 0, "{{$schools->count()}}", 0, 3, options);
            schools.start();
            var teachers = new CountUp("teachers", 0, "{{$teachers}}", 0, 3, options);
            teachers.start();
            var parents = new CountUp("parents", 0, "{{$parents}}", 0, 3, options);
            parents.start();
            var directions = new CountUp("directions", 0, "{{$directions}}", 0, 3, options);
            directions.start();

            var salary_by_month = [
                ['{{trans('dashboard.salary_by_month')}}'],
                    @foreach($per_month as $item)
                [{{$item['salary_by_month']}}],
                @endforeach
            ];
            var salary = c3.generate({
                bindto: '#salary_by_month',
                data: {
                    rows: salary_by_month,
                    type: 'area-spline'
                },
                color: {
                    pattern: ['#FD9883']
                },
                axis: {
                    x: {
                        tick: {
                            format: function (d) {
                                return formatMonth(d);
                            }
                        }
                    }
                },
                legend: {
                    show: true,
                    position: 'bottom'
                },
                padding: {
                    top: 10
                }
            });

            function formatMonth(d) {

                @foreach($per_month as $id => $item)
                if ('{{$id}}' == d) {
                    return '{{$item['month']}}' + ' ' + '{{$item['year']}}'
                }
                @endforeach
            }

            setTimeout(function () {
                salary.resize();
            }, 2000);

            setTimeout(function () {
                salary.resize();
            }, 4000);

            setTimeout(function () {
                salary.resize();
            }, 6000);
            $("[data-toggle='offcanvas']").click(function (e) {
                salary.resize();
            });

        });
    </script>
@stop
