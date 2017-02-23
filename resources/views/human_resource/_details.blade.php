<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="title">{{trans('human_resource.first_name')}}</label>

            <div class="controls">
                @if (isset($human_resource)) {{ $human_resource->first_name }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('human_resource.last_name')}}</label>

            <div class="controls">
                @if (isset($human_resource)) {{ $human_resource->last_name }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('human_resource.email')}}</label>

            <div class="controls">
                @if (isset($human_resource)) {{ $human_resource->email }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('human_resource.gender')}}</label>

            <div class="controls">
                @if (isset($human_resource)) {{ ($human_resource->gender=='1')?trans('human_resource.male'):trans('human_resource.female') }} @endif
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