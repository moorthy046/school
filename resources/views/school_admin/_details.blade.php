<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="title">{{trans('school_admin.first_name')}}</label>

            <div class="controls">
                @if (isset($school_admin)) {{ $school_admin->first_name }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('school_admin.last_name')}}</label>

            <div class="controls">
                @if (isset($school_admin)) {{ $school_admin->last_name }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('school_admin.email')}}</label>

            <div class="controls">
                @if (isset($school_admin)) {{ $school_admin->email }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('school_admin.gender')}}</label>

            <div class="controls">
                @if (isset($school_admin)) {{ ($school_admin->gender=='1')?trans('school_admin.male'):trans('school_admin.female') }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('school_admin.school')}}</label>

            <div class="controls">
                @if (isset($school)) {{ $school->title }} @endif
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                @if (@$action == 'show')
                    <a href="{{ url($type) }}" class="btn btn-primary">{{trans('table.close')}}</a>
                @else
                    <a href="{{ url($type) }}" class="btn btn-primary">{{trans('table.cancel')}}</a>
                    <button type="submit" class="btn btn-danger">{{trans('table.delete')}}</button>
                @endif
            </div>
        </div>
    </div>
</div>