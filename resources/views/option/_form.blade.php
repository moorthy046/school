<div class="panel panel-primary">
    <div class="panel-body">
        @if (isset($option))
            {!! Form::model($option, array('url' => $type . '/' . $option->id, 'method' => 'put', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => $type, 'method' => 'post', 'files'=> true)) !!}
        @endif
        <div class="form-group required {{ $errors->has('category') ? 'has-error' : '' }}">
            {!! Form::label('type', trans('option.category'), array('class' => 'control-label required')) !!}
            <div class="controls">
                {!! Form::select('category', $categories, null, array('id'=>'category', 'class' => 'form-control select2')) !!}
                <span class="help-block">{{ $errors->first('category', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
            {!! Form::label('title', trans('option.title'), array('class' => 'control-label required')) !!}
            <div class="controls">
                {!! Form::text('title', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('title', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('school_id') ? 'has-error' : '' }}">
            {!! Form::label('type', trans('option.school'), array('class' => 'control-label required')) !!}
            <div class="controls">
                {!! Form::select('school_id', $schools, null, array('id'=>'school_id', 'class' => 'form-control select2')) !!}
                <span class="help-block">{{ $errors->first('school_id', ':message') }}</span>
            </div>
        </div>
        <div class="form-group required {{ $errors->has('value') ? 'has-error' : '' }}">
            {!! Form::label('value', trans('option.value'), array('class' => 'control-label required')) !!}
            <div class="controls">
                {!! Form::text('value', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('value', ':message') }}</span>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-group">
            <div class="controls">
                <a href="{{ url($type) }}" class="btn btn-primary"><i
                            class="fa fa-arrow-left"></i> {{trans('table.back')}}</a>
                <button type="submit" class="btn btn-success"><i
                            class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
            </div>
        </div>
        <!-- ./ form actions -->

        {!! Form::close() !!}
    </div>
</div>
