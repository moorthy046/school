@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    {{trans('attendance.attendances_title')}}
    {!! Form::label('students', $current_student_group, array('class' => 'control-label')) !!}
    <div class="row">
        <div class="col-sm-3">
            <ul class="list-group">
                <li class="list-group-item disabled">
                    {{trans('attendance.students')}}
                </li>
                @foreach($students as $key =>$item)
                    <li id="{{$key}}" class="list-group-item">{{$item}}</li>
                @endforeach
            </ul>
        </div>
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-4">
                    {!! Form::label('date', trans('attendance.date'), array('class' => 'control-label')) !!}
                    {!! Form::text('date', date('d.m.Y.'), array('class' => 'form-control date')) !!}
                </div>
                <div class="col-sm-4">
                    {!! Form::label('hour', trans('attendance.hour'), array('class' => 'control-label')) !!}
                    {!! Form::select('hour[]', $hour_list, null, array('id'=>'hour','multiple'=>true, 'class' => 'form-control select2')) !!}
                </div>
                <div class="col-sm-4">
                    @if($justified_show>0)
                        {!! Form::label('justified', trans('attendance.justified'), array('class' => 'control-label')) !!}
                        {!! Form::select('justified', $justified_list, null, array('id'=>'justified', 'class' => 'form-control')) !!}
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-sm-8">
                    {!! Form::label('note', trans('attendance.comment'), array('class' => 'control-label')) !!}
                    {!! Form::textarea('note', null, array('id'=>'note', 'class' => 'form-control','size' => '30x5')) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <br>
                    <button type="button" class="btn btn-success add_attendance">{{trans('attendance.add_attendance')}}</button>
                </div>
            </div>
            <br>

            <div id="attendances">
                <table class="table table-bordered">
                    <thead>
                    <th class="col-sm-4">{{trans('attendance.student')}}</th>
                    <th class="col-sm-2">{{trans('attendance.hour')}}</th>
                    <th class="col-sm-2">{{trans('attendance.justified')}}</th>
                    @if($justified_show>0)<th class="col-sm-3">{{trans('attendance.delete')}}</th>@endif
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@stop

@section('scripts')
    <script>
        $('.list-group-item').not(".disabled").click(function () {
            $(this).toggleClass('active');
        });

        $('#date').change(function () {
            var date = $(this).val();
            if (date != "") {
                $('#hour').empty().select2("val", "");
                $.ajax({
                    type: "POST",
                    url: '{{ url('/attendance/hoursfordate') }}',
                    data: {_token: '{{ csrf_token() }}', date: date},
                    success: function (result) {
                        $.each(result, function (val, text) {
                            $('#hour').append($('<option></option>').val(text).html(text))
                        });
                    }
                });
                getattendance();
            }
        });
        $('.add_attendance').click(function () {
            var date = $('#date').val();
            var note = $('#note').val();
            var justified = $('#justified').val();
            var hour = $('#hour').val();
            var students = $.makeArray($('.list-group-item.active').map(function (index) {
                return this.id;
            }));
            if (hour.length > 0 && students.length > 0 && date.length > 0) {
                $.ajax({
                    type: "POST",
                    url: '{{ url('/attendance/add') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        date: date,
                        note: note,
                        justified: justified,
                        hour: hour,
                        students: students
                    },
                    success: function () {
                        $('.list-group-item').removeClass('active');
                        getattendance();
                    }
                })
            }
        });
        function getattendance() {
            $('#attendances tbody').empty();
            var date = $('#date').val();
            $.ajax({
                type: "POST",
                url: '{{ url('/attendance/attendance') }}',
                data: {_token: '{{ csrf_token() }}', date: date},
                success: function (result) {
                    data = $.parseJSON(result);
                    $.each(data, function (i, item) {
                        $('#attendances tbody').append('<tr><td>' + item.name + '</td><td>' + item.hour + '</td><td>' + item.justified + '</td><td>@if($justified_show>0)<span class="btn btn-success justify" id="' + item.id + '"><i class="fa fa-check"></i></span><span class="btn btn-warning no_justify" id="' + item.id + '"><i class="fa fa-times"></i></span><span class="btn btn-danger delete_attendance" id="' + item.id + '"><i class="fa fa-trash-o"></i></span>@endif</td></tr>');
                    });
                    justified_attendance();
                }
            })
        }

        function justified_attendance() {
            $('.delete_attendance').click(function () {
                var $attendance = $(this);
                var attendance_id = $attendance.attr('id');
                $.ajax({
                    type: "POST",
                    url: '{{ url('/attendance/delete') }}',
                    data: {_token: '{{ csrf_token() }}', id: attendance_id},
                    success: function (result) {
                        $attendance.parent().parent().remove();
                    }
                })
            })

            $('.no_justify').click(function () {
                var $attendance = $(this);
                var attendance_id = $attendance.attr('id');
                $.ajax({
                    type: "POST",
                    url: '{{ url('/attendance/justified') }}',
                    data: {_token: '{{ csrf_token() }}', id: attendance_id, justified:'no'},
                    success: function () {
                    }
                })
                getattendance();
            })
            $('.justify').click(function () {
                var $attendance = $(this);
                var attendance_id = $attendance.attr('id');
                $.ajax({
                    type: "POST",
                    url: '{{ url('/attendance/justified') }}',
                    data: {_token: '{{ csrf_token() }}', id: attendance_id, justified:'yes'},
                    success: function () {
                    }
                })
                getattendance();
            })
        }
    </script>
@endsection

