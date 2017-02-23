@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    {{trans('teachergroup.students_info')}}
    {!! Form::label('students', $teachergroup->title, array('class' => 'control-label')) !!}
    {!! Form::model($teachergroup, array('url' => url($type).'/'.$teachergroup->id.'/addstudents', 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
    <div class="form-group">
        <div class="controls">
            {!! Form::select('students_select[]', $students, $students_added, array('id'=>'students_select', 'multiple'=>true, 'class' => 'form-control select2')) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="controls">
            <a href="{{ url($type) }}"
               class="btn btn-warning">{{trans('table.back')}}</a>
            <button type="submit" class="btn btn-success">{{trans('table.ok')}}</button>
        </div>
    </div>
    {!! Form::close() !!}
@stop

@section('scripts')
    <script>
    </script>
@endsection

