@extends('layouts.update')
@section('content')
    <div class="step-content" style="padding-left: 15px; padding-top: 15px">
        <h3>{{trans('update.complete2')}}</h3>
        <hr>
        <p><strong>{{trans('update.well_done')}}</strong></p>
        <p>{{trans('update.successfully')}}</p>

        @if (is_writable(base_path()))
            <p>{!!trans('update.final_info')!!}</p>
        @endif
        <a class="btn btn-green pull-right" href="{{ url('/') }}">
            <i class="fa fa-sign-in"></i>
            {{trans('update.login')}}
        </a>
        <div class="clearfix"></div>
    </div>
@stop