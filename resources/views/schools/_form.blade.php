<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        @if (isset($school))
            {!! Form::model($school, array('url' => url($type) . '/' . $school->id, 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
        @endif
        <div class="form-group {{ $errors->has('title') ? 'has-error' : '' }}">
            {!! Form::label('title', trans('schools.title'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('title', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('title', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
            {!! Form::label('address', trans('schools.address'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('address', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('address', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
            {!! Form::label('phone', trans('schools.phone'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('phone', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
            {!! Form::label('email', trans('schools.email'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('email', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('email', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('student_card_prefix') ? 'has-error' : '' }}">
            {!! Form::label('student_card_prefix', trans('schools.student_card_prefix'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('student_card_prefix', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('student_card_prefix', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('student_card_prefix') ? 'has-error' : '' }}">
            {!! Form::label('student_card_background', trans('schools.student_card_background'), array('class' => 'control-label')) !!}
            <div class="controls">
                <div class="controls row" v-image-preview>
                    <img src="{{ url(isset($school->student_card_background)?$school->student_card_background:"") }}"
                         class="img-l col-sm-2">
                    {!! Form::file('student_card_background_file', null, array('id'=>'student_card_background_file', 'class' => 'form-control')) !!}
                    <img id="image-preview" width="300">
                    <span class="help-block">{{ $errors->first('student_card_background_file', ':message') }}</span>
                </div>
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
