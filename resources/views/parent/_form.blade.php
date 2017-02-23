<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        @if (isset($student_parent))
            {!! Form::model($student_parent, array('url' => url($type) . '/' . $student_parent->id, 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
            <div class="form-group {{ $errors->has('email') ? 'has-error' : '' }}">
                {!! Form::label('email', trans('parent.email'), array('class' => 'control-label')) !!}
                <div class="controls">
                    {!! Form::text('email', null, array('class' => 'form-control')) !!}
                    <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                </div>
            </div>
        @endif
        <div class="form-group {{ $errors->has('first_name') ? 'has-error' : '' }}">
            {!! Form::label('first_name', trans('parent.first_name'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('first_name', isset($student_parent->parent->first_name)?$student_parent->parent->first_name:"", array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('first_name', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('last_name') ? 'has-error' : '' }}">
            {!! Form::label('last_name', trans('parent.last_name'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('last_name', isset($student_parent->parent->last_name)?$student_parent->parent->last_name:"", array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('last_name', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
            {!! Form::label('address', trans('parent.address'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('address', isset($student_parent->parent->address)?$student_parent->parent->address:"", array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('address', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('mobile') ? 'has-error' : '' }}">
            {!! Form::label('mobile', trans('parent.mobile'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('mobile', isset($student_parent->parent->mobile)?$student_parent->parent->mobile:"", array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('mobile', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
            {!! Form::label('phone', trans('parent.phone'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('phone', isset($student_parent->parent->phone)?$student_parent->parent->phone:"", array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('phone', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('gender') ? 'has-error' : '' }}">
            {!! Form::label('gender', trans('parent.gender'), array('class' => 'control-label')) !!}
            <div class="controls radiobutton">
                {!! Form::label('gender', trans('parent.female'), array('class' => 'control-label')) !!}
                {!! Form::radio('gender', '0',(isset($student_parent->parent->gender) && $student_parent->parent->gender==0)?true:false) !!}
                {!! Form::label('gender', trans('parent.male'), array('class' => 'control-label')) !!}
                {!! Form::radio('gender', '1',(isset($student_parent->parent->gender) && $student_parent->parent->gender==1)?true:false) !!}
            </div>
        </div>
        <div class="form-group {{ $errors->has('birth_date') ? 'has-error' : '' }}">
            {!! Form::label('birth_date', trans('parent.birth_date'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('birth_date', isset($student_parent->parent->birth_date)?$student_parent->parent->birth_date:"", array('class' => 'form-control date')) !!}
                <span class="help-block">{{ $errors->first('birth_date', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('birth_city') ? 'has-error' : '' }}">
            {!! Form::label('birth_city', trans('parent.birth_city'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('birth_city', isset($student_parent->parent->birth_city)?$student_parent->parent->birth_city:"", array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('birth_city', ':message') }}</span>
            </div>
        </div>
        <div class="form-group {{ $errors->has('student_id') ? 'has-error' : '' }}">
            {!! Form::label('student_id', trans('parent.student'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::select('student_id[]', $students, (isset($students_ids)?$students_ids:null), array('id'=>'student_id', 'multiple'=>true, 'class' => 'form-control select2')) !!}
                <span class="help-block">{{ $errors->first('student_id', ':message') }}</span>
            </div>
        </div>
        @if (!isset($student_parent))
            <div class="form-group {{ $errors->has('password') ? 'has-error' : '' }}">
                {!! Form::label('password', trans('parent.password'), array('class' => 'control-label')) !!}
                <div class="controls">
                    {!! Form::password('password', array('class' => 'form-control')) !!}
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