@extends('layouts.frontend')
@section('content')
    <h1>{!! $page->title !!}</h1>
    <div class="row">
        <div class="col-md-12">
            <div class="details">
                {!! $page->content !!}
            </div>
        </div>
    </div>
@stop