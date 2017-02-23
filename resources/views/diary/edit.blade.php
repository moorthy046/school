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
	$('.date').on('change keyup paste', function() {
		var date = $(this).val();
		if (date != "") {
			$('#hour').empty().select2("val", "");
			$.ajax({
				type: "POST",
				url: '{{ url('/attendance/hoursfordate') }}',
				data: {_token: '{{ csrf_token() }}', date: date},
				success: function (result) {
					$('#hour').append($('<option></option>').val("").html("Select class"))
					$.each(result, function (val, text) {
						$('#hour').append($('<option></option>').val(text).html(text))
					});
				}
			});
		}
	});
</script>
@endsection