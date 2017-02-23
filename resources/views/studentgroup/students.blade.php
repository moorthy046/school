@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    {{trans('studentgroup.students_info')}}
    {!! Form::label('students', $studentgroup->title, array('class' => 'control-label')) !!}
    {!! Form::model($studentgroup, array('url' => url($type) . '/' . $section->id. '/' . $studentgroup->id.'/addstudents', 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
    <div class="form-group">
        <div class="controls">
            {!! Form::select('students_select[]', $students, null, array('id'=>'students_select', 'multiple'=>true, 'class' => 'form-control select2')) !!}
        </div>
    </div>
    <div class="form-group">
        <div class="controls">
            <a href="{{ url('/section/'.$section->id.'/groups') }}"
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

