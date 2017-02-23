<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">

        @if (isset($subject))
            {!! Form::model($subject, array('url' => url($type) . '/' . $subject->id, 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
        @endif
        <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
            {!! Form::label('title', trans('subject.title'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('title', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('title', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('direction_id') ? 'has-error' : '' }}">
            {!! Form::label('direction_id', trans('subject.direction'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::select('direction_id', array('' => trans('subject.select_direction')) + $directions, @isset($subject)? $subject->direction_id : 'default', array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('direction_id', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('order') ? 'has-error' : '' }}">
            {!! Form::label('order', trans('subject.order'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::input('number','order', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('order', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('class') ? 'has-error' : '' }}">
            {!! Form::label('class', trans('subject.class'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::input('number','class', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('class', ':message') }}</span>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <a href="{{ url($type) }}" class="btn btn-primary">{{trans('table.cancel')}}</a>
                <button type="submit" class="btn btn-success">{{trans('table.ok')}}</button>
            </div>
        </div>
        <!-- ./ form actions -->

        {!! Form::close() !!}
    </div>
</div>

