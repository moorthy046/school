@extends('layouts.auth')
@section('content')
    <div class="box-color">
        <h4>{{trans('auth.change_password')}}</h4>
        <br>
        {!! Form::open(array('url' => url('reset_password/'.$token), 'method' => 'post')) !!}
        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.email')) !!} :
            <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
            {!! Form::email('email', null, array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.password')) !!} :
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <span class="help-block">{{ $errors->first('password', ':message') }}</span>
            {!! Form::password('password', array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.password_confirmation')) !!} :
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
            <span class="help-block">{{ $errors->first('password_confirmation', ':message') }}</span>
            {!! Form::password('password_confirmation', array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        {!! Form::hidden('token', $token )!!}
        <div class="form-group">
            {!! Form::submit(trans('auth.reset'), ['class' => 'btn btn-primary btn-block']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection