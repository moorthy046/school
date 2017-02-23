@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

{{-- Content --}}
@section('content')
    @if(isset($teachergroup))
	{{trans('teachergroup.timetable_info')}}
	{!! Form::label('students', $teachergroup->title, array('class' => 'control-label')) !!}
    @endif
	<div class="row">
		<div class="col-sm-12">
			<div class="box-body">
				<table class="table table-bordered no-padding">
					<tbody>
					<tr>
						<th>#</th>
						<th width="16%">{{trans('teachergroup.monday')}}</th>
						<th width="16%">{{trans('teachergroup.tuesday')}}</th>
						<th width="16%">{{trans('teachergroup.wednesday')}}</th>
						<th width="16%">{{trans('teachergroup.thursday')}}</th>
						<th width="16%">{{trans('teachergroup.friday')}}</th>
                        <th width="16%">{{trans('teachergroup.saturday')}}</th>
					</tr>
						@for($i=1;$i<8;$i++)
                            <tr>
                                <td>{{$i}}</td>
                                @for($j=1;$j<7;$j++)
                                    <td id="{{$j}}-{{$i}}" class="droppable">
                                        @foreach($timetable as $item)
                                            @if($item['week_day']==$j && $item['hour']==$i)
                                                <div class="btn btn-default">
                                                    <span>{{$item['title']}}</span>
                                                    <br>
                                                    <span>{{$item['name']}}</span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </td>
                                @endfor
						    </tr>
                        @endfor
					</tbody>
				</table>
			</div>
		</div>
</div>
    @if(isset($teachergroup))
    <div class="form-group">
        <div class="controls">
            <a href="{{ url($type) }}" class="btn btn-warning">{{trans('table.back')}}</a>
        </div>
    </div>
    @endif
{!! Form::close() !!}
@stop


