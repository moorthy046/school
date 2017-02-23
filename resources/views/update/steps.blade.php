<li class="{{ isset($steps['welcome']) ? $steps['welcome'] : '' }}">
    <a>
        <div class="stepNumber"><i class="fa fa-home"></i>
            <span class="stepDesc text-small">{{trans('update.welcome')}}</span>
        </div>
    </a>
</li>
<li class="{{ isset($steps['complete']) ? $steps['complete'] : '' }}">
    <a>
        <div class="stepNumber"><i class="fa fa-flag-checkered"></i>
            <span class="stepDesc text-small">{{trans('update.complete')}}</span>
        </div>
    </a>
</li>
