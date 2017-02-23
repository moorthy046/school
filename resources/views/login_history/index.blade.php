@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    @if(Sentinel::inRole('teacher'))
        <div class="page-header clearfix">
            <div class="pull-right">
                <a href="{{ url($type.'/create') }}" class="btn btn-sm btn-primary">
                    <i class="fa fa-plus-circle"></i> {{ trans('table.new') }}</a>
            </div>
        </div>
    @endif
    <table id="data" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>{{ trans('login_history.user') }}</th>
            <th>{{ trans('login_history.ip_address') }}</th>
            <th>{{ trans('login_history.date_time') }}</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
@stop

{{-- Scripts --}}
@section('scripts')

@stop