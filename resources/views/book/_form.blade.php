<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        @if (isset($book))
            {!! Form::model($book, array('url' => url($type) . '/' . $book->id, 'method' => 'put', 'class' => 'bf', 'files'=> true)) !!}
        @else
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
        @endif
        <div class="form-group required {{ $errors->has('title') ? 'has-error' : '' }}">
            {!! Form::label('title', trans('book.title'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('title', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('title', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('internal') ? 'has-error' : '' }}">
            {!! Form::label('internal', trans('book.internal'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('internal', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('internal', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('subject_id') ? 'has-error' : '' }}">
            {!! Form::label('subject_id', trans('book.subject'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::select('subject_id', array('' => trans('book.select_subject')) + $subjects, @isset($book)? $book->subject_id : 'default', array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('subject_id', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('author') ? 'has-error' : '' }}">
            {!! Form::label('author', trans('book.author'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('author', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('author', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('year') ? 'has-error' : '' }}">
            {!! Form::label('year', trans('book.year'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('year', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('year', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('quantity') ? 'has-error' : '' }}">
            {!! Form::label('quantity', trans('book.quantity'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('quantity', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('quantity', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('version') ? 'has-error' : '' }}">
            {!! Form::label('version', trans('book.version'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('version', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('version', ':message') }}</span>
            </div>
        </div>

        <div class="form-group  {{ $errors->has('publisher') ? 'has-error' : '' }}">
            {!! Form::label('publisher', trans('book.publisher'), array('class' => 'control-label')) !!}
            <div class="controls">
                {!! Form::text('publisher', null, array('class' => 'form-control')) !!}
                <span class="help-block">{{ $errors->first('publisher', ':message') }}</span>
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
