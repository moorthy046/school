@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
	{{trans('studentgroup.subjects_info')}}
	{!! Form::label('students', $studentgroup->title, array('class' => 'control-label')) !!}
	<div class="box">
		<div class="box-body no-padding">
			<table class="table table-condensed">
				<tbody>
				<tr>
					<th>#</th>
					<th width="20%">{{trans('studentgroup.subjects')}}</th>
					<th width="60%">{{trans('studentgroup.teachers')}}</th>
				</tr>
				@foreach($subjects as $key=>$subject)
					<tr>
						<td>{{$key+1}}.</td>
						<td>{{$subject->title}}</td>
						<td>
                            {!! Form::model($subject, array('url' => url($type) . '/' . $subject->id. '/' . $studentgroup->id.'/addeditsubject', 'method' => 'put', 'id' => 'bf-'.$subject->id, 'files'=> true)) !!}
                            {!! Form::select('teachers_select[]', $teachers, $teacher_subject[$subject->id], array('id'=>'teachers_select'.$subject->id, 'multiple'=>true, 'class' => 'form-control select2')) !!}
                            {!! Form::close() !!}
						</td>
					</tr>
				@endforeach
				</tbody>
			</table>
		</div><!-- /.box-body -->
	</div>

	<div class="form-group">
		<div class="controls">
			<a href="{{ url('/section/'.$section->id.'/groups') }}" class="btn btn-warning">{{trans('table.back')}}</a>
		</div>
	</div>
	<!-- ./ form actions -->

{!! Form::close() !!}
@stop

@section('scripts')
    <script>
        $("[id^='teachers_select']").on("select2:select select2:unselect", function(e) {
            var $form_id = $(this).parent();
            $.ajax({
                type: "POST",
                url: $form_id.attr('action'),
                data: $form_id.serialize(),
                success: function (response) {
                }
            });
        });
    </script>
@endsection

