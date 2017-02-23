@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
	{{ $title }}
@stop

{{-- Content --}}
@section('content')
<div class="row">
	<div class="col-md-12">
	    <div class="row">
            {!! Form::open(array('url' => url($type.'/'.$schoolyear->id.'/post_data'), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}

            <div class="form-group {{ $errors->has('select_school_year_id') ? 'has-error' : '' }}">
                {!! Form::label('select_school_year_id', trans('schoolyear.schoolyear_select_info'), array('class' => 'control-label')) !!}
                <div class="controls">
                    {!! Form::select('select_school_year_id', $school_year_list, null, array('id'=>'select_school_year_id', 'class' => 'form-control')) !!}
                    <span class="help-block">{{ $errors->first('select_school_year_id', ':message') }}</span>
                </div>
            </div>
            <p><span class="label label-info">{{trans('schoolyear.type_new_names').$schoolyear->title}}</span></p>
            <p><span class="label label-danger">{{trans('schoolyear.type_new_names_continue').$schoolyear->title}}</span></p>
            <div id="sections">

            </div>
            <!-- Form Actions -->
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success">{{trans('table.ok')}}</button>
                </div>
            </div>
            <!-- ./ form actions -->

            {!! Form::close() !!}

        </div>
	</div>
</div>
@stop

@section('scripts')
	<script>
        $( document ).ready(function() {
            $("#select_school_year_id").change(function() {
                $('#sections').html('');
                if ($(this).val() != "") {
                    $.ajax({
                        type: "GET",
                        url: '{{url('/schoolyear')}}/' + $(this).val() + '/get_sections',
                        success: function (response) {
                            var sections = '';
                            $.each(response, function (key, val) {
                                sections += '<label class="control-label">' + val + '</label>' +
                                        '<input type="text" id="section" name="section[' + key + ']" class="form-control">'
                            })
                            $('#sections').html(sections);

                        }
                    });
                }
            });
        })
	</script>
@endsection