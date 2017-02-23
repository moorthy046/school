@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    <table id="data" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>{{ trans('borrowedbook.title') }}</th>
            <th>{{ trans('borrowedbook.author') }}</th>
            <th>{{ trans('borrowedbook.borrowed') }}</th>

         </tr>
         </thead>
         <tbody>
         </tbody>
     </table>
 @stop

 {{-- Scripts --}}
@section('scripts')

@stop