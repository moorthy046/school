@extends('layouts.auth')
@section('content')
    <div class="box-color">
        <h4>{{trans('auth.sign_account')}}</h4>
        <br>
        {!! Form::open(array('url' => url('signin'), 'method' => 'post', 'name' => 'form')) !!}
        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.email')) !!} :
            <span class="help-block">{{ $errors->first('email', ':message') }}</span>
            {!! Form::email('email', null, array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
            {!! Form::label(trans('auth.password')) !!} :
            <span class="help-block">{{ $errors->first('password', ':message') }}</span>
            {!! Form::password('password', array('class' => 'form-control', 'required'=>'required')) !!}
        </div>
        <button type="submit" class="btn btn-primary btn-block">{{trans('auth.login')}}</button>
        {!! Form::close() !!}
        <div class="text-center">
            <h5><a href="{{url('forgot')}}" class="text-primary _600">{{trans('auth.forgot')}}?</a></h5>
            @if(Settings::get('self_registration')=='yes')
                <h5><a href="{{url('register')}}" class="text-primary _600">{{trans('auth.create_account')}}</a></h5>
            @endif
        </div>
    </div>
@stop