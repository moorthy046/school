@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
	{{ $title }}
@stop

{{-- Content --}}
@section('content')
    <!-- ./ notifications -->
	@include($type.'/_form')
@stop

@section('scripts')
<script>
	$('#direction_id').change(function () {
		$('#class').empty().select2("val", "");
		if ($(this).val() != "") {
			$.ajax({
				type: "GET",
				url: '{{ url('/studentgroup/duration') }}',
				data: {_token: '{{ csrf_token() }}', direction: $(this).val()},
				success: function (result) {
                    $('#class').append($('<option></option>').val('').html("{!! trans('studentgroup.select_class') !!}")).select2("val", "");
					for(var i=1;i<=result;i++) {
						$('#class').append($('<option></option>').val(i).html(i));
					}
				}
			});
		}
	});
</script>
@endsection
