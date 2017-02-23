<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="title">{{trans('dormitorybed.title')}}</label>

            <div class="controls">
                @if (isset($bed))
                    {{ $bed->title }}
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="direction">{{trans('dormitorybed.room')}}</label>

            <div class="controls">
                @if (isset($bed) && $bed->dormitory_room_id != null)
                    {{ $bed->dormitory_room()->first()->title }}
                @endif
            </div>
        </div>

        <div class="form-group">
            <label class="control-label" for="direction">{{trans('dormitorybed.student')}}</label>

            <div class="controls">
                @if (isset($bed) && $bed->student_id != null)
                    {{ $bed->getNameOfStudent() }}
                @endif
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