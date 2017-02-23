<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="student">{{trans('reservedbook.user_reserved')}}</label>

            <div class="controls">
                @if (isset($bookuser->user->id))
                    {{ $bookuser->user->full_name }}
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="book">{{trans('reservedbook.book')}}</label>

            <div class="controls">
                @if (isset($bookuser->book->id))
                    {{ $bookuser->book->title }}
                @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="reserved">{{trans('reservedbook.reserved')}}</label>
            <div class="controls">
                {{ $bookuser->reserved }}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="reserved">{{trans('reservedbook.available')}}</label>
            <div class="controls">
                {{ $available }}
            </div>
        </div>
        <div class="form-group">
            <div class="controls">
                <a href="{{ url($type) }}" class="btn btn-primary">{{trans('table.cancel')}}</a>
                @if (@$action == 'issue')
                    <button type="submit"
                            class="btn btn-success" {{ (isset($available) && $available < 1) ? 'disabled' : '' }}>{{trans('reservedbook.issue')}}</button>
                @else
                    <button type="submit" class="btn btn-danger">{{trans('reservedbook.delete')}}</button>
                @endif

            </div>
        </div>
    </div>
</div>