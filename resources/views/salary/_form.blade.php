<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        @if (isset($salary))
            {!! Form::model($salary, array('url' => url($type) . '/' . $salary->id, 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
        @endif
        <div class="form-group required {{ $errors->has('salary') ? 'has-error' : '' }}">
            {!! Form::label('salary', trans('salary.salary'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::input('number','salary', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('salary', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('date') ? 'has-error' : '' }}">
            {!! Form::label('date', trans('salary.date'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('date', null, array('class' => 'form-control date')) !!}
                <span class="help-block">{{ $errors->first('date', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('user_id') ? 'has-error' : '' }}">
            {!! Form::label('user_id', trans('salary.staff'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::select('user_id', $users, null, array('id'=>'user_id', 'class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('user_id', ':message') }}</span>
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
