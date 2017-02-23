<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="title">{{trans('sms_message.text')}}</label>
            <div class="controls">
                {{ $sms_message->text }}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('sms_message.receiver')}}</label>
            <div class="controls">
                {{ isset($sms_message->user->full_name)?$sms_message->user->full_name:"" }}
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('sms_message.number')}}</label>
            <div class="controls">
                {{ $sms_message->number }}
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