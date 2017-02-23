@extends('layouts.auth')
@section('content')
    <div class="box-color">
        <h4 class="text-center">{{trans('auth.create_account')}}</h4>
        <br>
        {!! Form::open(array('url' => url('signup'), 'method' => 'post')) !!}

        <div class="form-group has-feedback {{ $errors->has('first_name') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.first_name')) !!} :
            <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
            {!! Form::text('first_name', null, array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <div class="form-group has-feedback {{ $errors->has('last_name') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.last_name')) !!} :
            <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
            {!! Form::text('last_name', null, array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <div class="form-group has-feedback {{ $errors->has('email') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.email')) !!} :
            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
            {!! Form::email('email', null, array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <div class="form-group has-feedback {{ $errors->has('password') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.password')) !!} :
            <span class="help-block">{{ $errors->first('password', ':message') }}</span>
            {!! Form::password('password', array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <div class="form-group has-feedback {{ $errors->has('password_confirmation') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.password_confirmation')) !!} :
            <span class="help-block">{{ $errors->first('password_confirmation', ':message') }}</span>
            {!! Form::password('password_confirmation', array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
            <button type="submit" class="btn btn-primary btn-block">{{trans('auth.register')}}</button>
        {!! Form::close() !!}

        <h5 class="text-center text-default"><a href="{{url('signin')}}" class="text-primary _600">{{trans('auth.login')}}?</a>
        </h5>
    </div>
@stop