@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
	{{ $title }}
@stop

{{-- Content --}}
@section('content')
<div class="row">
	<div class="col-md-12">
	    <div class="details">
		    @include('/transportation/_details')
		</div>
	</div>
</div>
@stop