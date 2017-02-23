<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        @if (isset($school_admin))
            {!! Form::model($school_admin, array('url' => url($type) . '/' . $school_admin->id, 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
        @endif
        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            {!! Form::label('email', trans('school_admin.email'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('email', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('email', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
            {!! Form::label('first_name', trans('school_admin.first_name'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('first_name', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
            {!! Form::label('last_name', trans('school_admin.last_name'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('last_name', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
            {!! Form::label('address', trans('school_admin.address'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('address', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('address', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
            {!! Form::label('mobile', trans('school_admin.mobile'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('mobile', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('mobile', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('gender') ? 'has-error' : '' }}">
            {!! Form::label('gender', trans('school_admin.gender'), array('class' => 'control-label')) !!}
            <div class="controls radiobutton">
                {!! Form::label('gender', trans('school_admin.female'), array('class' => 'control-label')) !!}
                {!! Form::radio('gender', '0') !!}
                {!! Form::label('gender', trans('school_admin.male'), array('class' => 'control-label')) !!}
                {!! Form::radio('gender', '1') !!}
            </div>
        </div>
        <div class="form-group {{ $errors->has('birth_date') ? 'has-error' : '' }}">
            {!! Form::label('birth_date', trans('school_admin.birth_date'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('birth_date', null, array('class' => 'form-control date')) !!}
                <span class="help-block">{{ $errors->first('birth_date', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('birth_city') ? 'has-error' : '' }}">
            {!! Form::label('birth_city', trans('school_admin.birth_city'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('birth_city', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('birth_city', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('school_id') ? 'has-error' : '' }}">
            {!! Form::label('school_id', trans('school_admin.school'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::select('school_id', array('' => trans('school_admin.select_school')) + $school_ids, @isset($school_id)? $school_id : '', array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('school_id', ':message') }}</span>
            </div>
        </div>
        @if (!isset($school_admin))
            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                {!! Form::label('password', trans('school_admin.password'), array('class' => 'control-label')) !!}
                <div class="controls">
                    {!! Form::text('password', "", array('class' => 'form-control')) !!}
                    <span class="help-block">{{ $errors->first('password', ':message') }}</span>
                </div>
            </div>
            @endif

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