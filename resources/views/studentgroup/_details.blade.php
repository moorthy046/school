<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="title">{{trans('studentgroup.name')}}</label>

            <div class="controls">
                @if (isset($studentgroup)) {{ $studentgroup->title }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="class">{{trans('studentgroup.class')}}</label>

            <div class="controls">
                @if (isset($studentgroup)) {{ $studentgroup->class }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('studentgroup.direction')}}</label>

            <div class="controls">
                @if (isset($studentgroup)) {{ $studentgroup->direction->title }} @endif
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                @if (@$action == 'show')
                    <a href="{{ url('/section/'.$section->id.'/groups') }}"
                       class="btn btn-primary">{{trans('table.close')}}</a>
                @else
                    <a href="{{ url('/section/'.$section->id.'/groups') }}"
                       class="btn btn-primary">{{trans('table.cancel')}}</a>
                    <button type="submit" class="btn btn-danger">{{trans('table.delete')}}</button>
                @endif
            </div>
        </div>
    </div>
</div>