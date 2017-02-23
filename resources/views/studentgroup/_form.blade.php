<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        @if (isset($studentgroup))
            {!! Form::model($studentgroup, array('url' => url($type) . '/' . $studentgroup->id, 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
        @endif
        <input type="hidden" value="{{$section->id}}" name="section_id">

        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
            {!! Form::label('title', trans('studentgroup.name'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('title', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('title', ':message') }}</span>
            </div>
        </div>
        @if (!isset($studentgroup))
            <div class="form-group {{ $errors->has('direction_id') ? 'has-error' : '' }}">
                {!! Form::label('direction_id', trans('studentgroup.direction'), array('class' => 'control-label')) !!}
                <div class="controls">
                    {!! Form::select('direction_id', $directions, null, array('id'=>'direction_id', 'class' => 'form-control select2')) !!}
                    <span class="help-block">{{ $errors->first('direction_id', ':message') }}</span>
                </div>
            </div>
            <div class="form-group {{ $errors->has('class') ? 'has-error' : '' }}">
                {!! Form::label('class', trans('studentgroup.class'), array('class' => 'control-label')) !!}
                <div class="controls">
                    {!! Form::select('class', array(), null, array('id'=>'class', 'class' => 'form-control select2')) !!}
                    <span class="help-block">{{ $errors->first('class', ':message') }}</span>
                </div>
            </div>
            @endif
                    <!-- Form Actions -->
            <div class="form-group">
                <div class="controls">
                    <a href="{{ url('/section/'.$section->id.'/groups') }}"
                       class="btn btn-primary">{{trans('table.cancel')}}</a>
                    <button type="submit" class="btn btn-success">{{trans('table.ok')}}</button>
                </div>
            </div>
            <!-- ./ form actions -->

            {!! Form::close() !!}
    </div>
</div>
