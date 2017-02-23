<div class="panel panel-success">
    <div class="panel-heading">
        <div class="panel-title"> {{$title}}</div>
    </div>
    <div class="panel-body">
        <div class="form-group">
            <label class="control-label" for="title">{{trans('parent.first_name')}}</label>

            <div class="controls">
                @if (isset($student_parent)) {{ $student_parent->parent->first_name }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('parent.last_name')}}</label>

            <div class="controls">
                @if (isset($student_parent)) {{ $student_parent->parent->last_name }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('parent.email')}}</label>

            <div class="controls">
                @if (isset($student_parent)) {{ $student_parent->parent->email }} @endif
            </div>
        </div>
        <div class="form-group">
            <label class="control-label" for="title">{{trans('parent.gender')}}</label>

            <div class="controls">
                @if (isset($student_parent)) {{ ($student_parent->parent->gender=='1')?trans('parent.male'):trans('parent.female') }} @endif
            </div>
        </div>
        @if($student_parent->students->count()>0)
            <div class="form-group">
                <label class="control-label" for="title">{{trans('parent.students')}}</label>
                <div class="controls">
                    <ul>
                        @foreach($student_parent->students as $item)
                            @if(isset($item->full_name))
                                <li>{{ $item->full_name }}</li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
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