@extends('layouts.secure')

{{-- Web site Title --}}
@section('title')
    {{ $title }}
@stop

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/icheck.css') }}" type="text/css">
@stop
{{-- Content --}}
@section('content')
    <link rel="stylesheet" href="{{ asset('css/icheck.css') }}" type="text/css">
    <div class="panel panel-success">
        <div class="panel-body">
            <span class="pull-right">
                <a href="#" class="text-muted">
                    <i class="fa fa-gear"></i>
                </a>
            </span>
            {!! Form::open(array('url' => url($type), 'method' => 'post', 'class' => 'bf', 'files'=> true)) !!}
            <div class="nav-tabs-custom" id="setting_tabs">
                <ul class="nav nav-tabs" style="display:list-item;">
                    <li class="active">
                        <a href="#general_configuration"
                           data-toggle="tab" title="{{ trans('settings.general_configuration') }}"><i class="material-icons md-24">build</i></a>
                    </li>
                    <li>
                        <a href="#email_configuration"
                           data-toggle="tab" title="{{ trans('settings.email_configuration') }}"><i
                                    class="material-icons md-24">email</i></a>
                    </li>
                    <li>
                        <a href="#pusher_configuration"
                           data-toggle="tab" title="{{ trans('settings.pusher_configuration') }}"><i class="material-icons md-24">receipt</i></a>
                    </li>
                    <li>
                        <a href="#paypal_settings"
                           data-toggle="tab" title="{{ trans('settings.paypal_settings') }}"><i class="material-icons md-24">payment</i></a>
                    </li>
                    <li>
                        <a href="#stripe_settings"
                           data-toggle="tab" title="{{ trans('settings.stripe_settings') }}"><i class="material-icons md-24">vpn_key</i></a>
                    </li>
                    <li>
                        <a href="#sms_settings"
                           data-toggle="tab" title="{{ trans('settings.sms_settings') }}"><i class="material-icons md-24">phone</i></a>
                    </li>
                    <li>
                        <a href="#backup_configuration"
                           data-toggle="tab" title="{{ trans('settings.backup_configuration') }}"><i class="material-icons md-24">backup</i></a>
                    </li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="general_configuration">
                        <div class="form-group required {{ $errors->has('logo') ? 'has-error' : '' }} ">
                            {!! Form::label('logo_file', trans('settings.logo'), array('class' => 'control-label')) !!}
                            <div class="controls row" v-image-preview>
                                <img src="{{ url('uploads/site/'.Settings::get('logo')) }}"
                                     class="img-l col-sm-2">
                                {!! Form::file('logo_file', null, array('id'=>'logo_file', 'class' => 'form-control')) !!}
                                <img id="image-preview" width="300">
                                <span class="help-block">{{ $errors->first('logo_file', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('name') ? 'has-error' : '' }}">
                            {!! Form::label('name', trans('settings.name'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::text('name', old('name', Settings::get('name')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('name', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('email') ? 'has-error' : '' }}">
                            {!! Form::label('email', trans('settings.email'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::text('email', old('email', Settings::get('email')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('email', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('allowed_extensions_avatar') ? 'has-error' : '' }}">
                            {!! Form::label('allowed_extensions_avatar', trans('settings.allowed_extensions_avatar'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::text('allowed_extensions_avatar', old('allowed_extensions_avatar', Settings::get('allowed_extensions_avatar')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('max_upload_avatar_size', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('max_upload_avatar_size') ? 'has-error' : '' }}">
                            {!! Form::label('max_upload_avatar_size', trans('settings.max_upload_avatar_size'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::select('max_upload_avatar_size', $max_upload_file_size, old('max_upload_avatar_size', Settings::get('max_upload_avatar_size')), array('id'=>'max_upload_file_size','class' => 'form-control select2')) !!}
                                <span class="help-block">{{ $errors->first('max_upload_avatar_size', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('minimum_characters') ? 'has-error' : '' }}">
                            {!! Form::label('minimum_characters', trans('settings.minimum_characters'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::text('minimum_characters', old('minimum_characters', Settings::get('minimum_characters')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('minimum_characters', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('upload_webcam') ? 'has-error' : '' }}">
                            {!! Form::label('upload_webcam', trans('settings.upload_webcam'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                <div class="form-inline">
                                    <div class="radio">
                                        {!! Form::radio('upload_webcam', 'upload',(Settings::get('upload_webcam')=='upload')?true:false,array('class' => 'upload_webcam'))  !!}
                                        {!! Form::label('true', 'Upload')  !!}
                                    </div>
                                    <div class="radio">
                                        {!! Form::radio('upload_webcam', 'webcam', (Settings::get('upload_webcam')=='webcam')?true:false,array('class' => 'upload_webcam'))  !!}
                                        {!! Form::label('false', 'Webcam') !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('date_format') ? 'has-error' : '' }}">
                            {!! Form::label('date_format', trans('settings.date_format'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                <div class="radio">
                                    {!! Form::radio('date_format', 'F j,Y',(Settings::get('date_format')=='F j,Y')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('true', date('F j,Y'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'Y-d-m',(Settings::get('date_format')=='Y-d-m')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('date_format', date('Y-d-m'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'd.m.Y.',(Settings::get('date_format')=='d.m.Y.')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('date_format', date('d.m.Y.'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'd.m.Y',(Settings::get('date_format')=='d.m.Y')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('date_format', date('d.m.Y'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'd/m/Y',(Settings::get('date_format')=='d/m/Y')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('date_format', date('d/m/Y'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('date_format', 'm/d/Y',(Settings::get('date_format')=='m/d/Y')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('date_format', date('m/d/Y'))  !!}
                                </div>
                                <div class="form-inline">
                                    {!! Form::radio('date_format', '',false,array('class' => 'icheck', 'id'=>'date_format_custom_radio'))  !!}
                                    {!! Form::label('custom_format', trans('settings.custom_format'))  !!}
                                    {!! Form::input('text','date_format_custom', Settings::get('date_format'), array('class' => 'form-control')) !!}
                                </div>
                            </div>
                            <span class="help-block">{{ $errors->first('date_format', ':message') }}</span>
                        </div>
                        <a href="{{url('http://php.net/manual/en/function.date.php')}}">{!! trans('settings.date_time_format') !!}</a>
                        <div class="form-group required {{ $errors->has('time_format') ? 'has-error' : '' }}">
                            {!! Form::label('time_format', trans('settings.time_format'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                <div class="radio">
                                    {!! Form::radio('time_format', 'g:i a',(Settings::get('time_format')=='g:i a')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('time_format', date('g:i a'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('time_format', 'g:i A',(Settings::get('time_format')=='g:i A')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('time_format', date('g:i A'))  !!}
                                </div>
                                <div class="radio">
                                    {!! Form::radio('time_format', 'H:i',(Settings::get('time_format')=='H:i')?true:false,array('class' => 'icheck'))  !!}
                                    {!! Form::label('time_format', date('H:i'))  !!}
                                </div>
                                <div class="form-inline">
                                    {!! Form::radio('time_format', '',false,array('class' => 'icheck', 'id'=>'time_format_custom_radio'))  !!}
                                    {!! Form::label('custom_format', trans('settings.custom_format'))  !!}
                                    {!! Form::input('text','time_format_custom', Settings::get('time_format'), array('class' => 'form-control')) !!}
                                </div>
                            </div>
                            <span class="help-block">{{ $errors->first('date_format', ':message') }}</span>
                        </div>
                        <div class="form-group required {{ $errors->has('currency') ? 'has-error' : '' }}">
                            {!! Form::label('currency', trans('settings.currency'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::select('currency', $options['currency'], old('currency', Settings::get('currency')), array('id'=>'currency','class' => 'form-control select2')) !!}
                                <span class="help-block">{{ $errors->first('currency', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('self_registration') ? 'has-error' : '' }}">
                            {!! Form::label('self_registration', trans('settings.self_registration'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                <div class="form-inline">
                                    <div class="radio">
                                        {!! Form::radio('self_registration', 'no',(Settings::get('self_registration')=='no')?true:false,array('id'=>'self_registration_no','class' => 'self_registration'))  !!}
                                        {!! Form::label('true', 'NO')  !!}
                                    </div>
                                    <div class="radio">
                                        {!! Form::radio('self_registration', 'yes', (Settings::get('self_registration')=='yes')?true:false,array('id'=>'self_registration_yes','class' => 'self_registration'))  !!}
                                        {!! Form::label('false', 'YES') !!}
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('self_registration', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required self_registration_role {{ $errors->has('self_registration_role') ? 'has-error' : '' }}">
                            {!! Form::label('self_registration_role', trans('settings.self_registration_role'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::select('self_registration_role', $self_registration_role, old('self_registration_role', Settings::get('self_registration_role')), array('id'=>'self_registration_role','class' => 'form-control select2')) !!}
                                <span class="help-block">{{ $errors->first('self_registration_role', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('visitor_card_prefix') ? 'has-error' : '' }}">
                            {!! Form::label('visitor_card_prefix', trans('settings.visitor_card_prefix'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::text('visitor_card_prefix', old('visitor_card_prefix', Settings::get('visitor_card_prefix')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('visitor_card_prefix', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('visitor_card_background') ? 'has-error' : '' }}">
                            {!! Form::label('visitor_card_background', trans('settings.visitor_card_background'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                <div class="controls row">
                                    <div class="col-sm-3">
                                        <img src="{{ url('uploads/visitor_card/'.Settings::get('visitor_card_background')) }}" alt="Visitor card background"
                                             class="img-thumbnail">
                                    </div>
                                    <div class="col-sm-9">
                                        <input type="file" name="visitor_card_background_file">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="tab-pane" id="email_configuration">
                        <div class="form-group required {{ $errors->has('email_driver') ? 'has-error' : '' }}">
                            {!! Form::label('email_driver', trans('settings.email_driver'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                <div class="form-inline">
                                    <div class="radio">
                                        {!! Form::radio('email_driver', 'mail',(Settings::get('email_driver')=='mail')?true:false,array('id'=>'mail','class' => 'email_driver'))  !!}
                                        {!! Form::label('true', 'MAIL')  !!}
                                    </div>
                                    <div class="radio">
                                        {!! Form::radio('email_driver', 'smtp', (Settings::get('email_driver')=='smtp')?true:false,array('id'=>'smtp','class' => 'email_driver'))  !!}
                                        {!! Form::label('false', 'SMTP') !!}
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('email_driver', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group smtp required {{ $errors->has('email_host') ? 'has-error' : '' }}">
                            {!! Form::label('email_host', trans('settings.email_host'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','email_host', old('email_host', Settings::get('email_host')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('email_host', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group smtp required {{ $errors->has('email_port') ? 'has-error' : '' }}">
                            {!! Form::label('email_port', trans('settings.email_port'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','email_port', old('email_port', Settings::get('email_port')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('email_port', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group smtp required {{ $errors->has('email_username') ? 'has-error' : '' }}">
                            {!! Form::label('email_username', trans('settings.email_username'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','email_username', old('email_username', Settings::get('email_username')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('email_username', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group smtp required {{ $errors->has('email_password') ? 'has-error' : '' }}">
                            {!! Form::label('email_password', trans('settings.email_password'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','email_password', old('email_password', Settings::get('email_password')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('email_password', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="pusher_configuration">
                        <div class="form-group required {{ $errors->has('pusher_app_id') ? 'has-error' : '' }}">
                            {!! Form::label('pusher_app_id', trans('settings.pusher_app_id'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','pusher_app_id', old('pusher_app_id', Settings::get('pusher_app_id')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('pusher_app_id', ':message') }}</span>
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->has('pusher_key') ? 'has-error' : '' }}">
                            {!! Form::label('pusher_key', trans('settings.pusher_key'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','pusher_key', old('pusher_key', Settings::get('pusher_key')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('pusher_key', ':message') }}</span>
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->has('pusher_secret') ? 'has-error' : '' }}">
                            {!! Form::label('pusher_secret', trans('settings.pusher_secret'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','pusher_secret', old('pusher_secret', Settings::get('pusher_secret')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('pusher_secret', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="paypal_settings">
                        <div class="form-group required {{ $errors->has('paypal_testmode') ? 'has-error' : '' }}">
                            {!! Form::label('paypal_testmode', trans('settings.paypal_testmode'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                <div class="form-inline">
                                    <div class="radio">
                                        {!! Form::radio('paypal_testmode', 'true',(Settings::get('paypal_testmode')=='true')?true:false,array('class' => 'icheck'))  !!}
                                        {!! Form::label('true', 'True')  !!}
                                    </div>
                                    <div class="radio">
                                        {!! Form::radio('paypal_testmode', 'false', (Settings::get('paypal_testmode')=='false')?true:false,array('class' => 'icheck'))  !!}
                                        {!! Form::label('false', 'False') !!}
                                    </div>
                                </div>
                                <span class="help-block">{{ $errors->first('paypal_testmode', ':message') }}</span>
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->has('paypal_username') ? 'has-error' : '' }}">
                            {!! Form::label('paypal_username', trans('settings.paypal_username'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','paypal_username', old('paypal_username', Settings::get('paypal_username')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('paypal_username', ':message') }}</span>
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->has('paypal_password') ? 'has-error' : '' }}">
                            {!! Form::label('paypal_password', trans('settings.paypal_password'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','paypal_password', old('paypal_password', Settings::get('paypal_password')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('paypal_password', ':message') }}</span>
                            </div>
                        </div>

                        <div class="form-group required {{ $errors->has('paypal_signature') ? 'has-error' : '' }}">
                            {!! Form::label('paypal_signature', trans('settings.paypal_signature'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','paypal_signature', old('paypal_signature', Settings::get('paypal_signature')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('paypal_signature', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="stripe_settings">
                        <div class="form-group required {{ $errors->has('stripe_secret') ? 'has-error' : '' }}">
                            {!! Form::label('stripe_secret', trans('settings.stripe_publishable'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','stripe_secret', old('stripe_secret', Settings::get('stripe_secret')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('stripe_secret', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('stripe_publishable') ? 'has-error' : '' }}">
                            {!! Form::label('stripe_publishable', trans('settings.stripe_secret'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','stripe_publishable', old('stripe_publishable', Settings::get('stripe_publishable')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('stripe_publishable', ':message') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="sms_settings">
                        <legend>{{trans('settings.twilio')}}</legend>
                        <div class="form-group required {{ $errors->has('twilio_sid') ? 'has-error' : '' }}">
                            {!! Form::label('twilio_sid', trans('settings.twilio_sid'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','twilio_sid', old('twilio_sid', Settings::get('twilio_sid')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('twilio_sid', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('twilio_token') ? 'has-error' : '' }}">
                            {!! Form::label('twilio_token', trans('settings.twilio_token'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','twilio_token', old('twilio_token', Settings::get('twilio_token')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('twilio_token', ':message') }}</span>
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->has('twilio_from') ? 'has-error' : '' }}">
                            {!! Form::label('twilio_from', trans('settings.twilio_from'), array('class' => 'control-label')) !!}
                            <div class="controls">
                                {!! Form::input('text','twilio_from', old('twilio_from', Settings::get('twilio_from')), array('class' => 'form-control')) !!}
                                <span class="help-block">{{ $errors->first('twilio_from', ':message') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="backup_configuration">
                        <backup-settings backup_type="{{ Settings::get('backup_type') }}"
                                         :options="{{ $opts['backup_type'] }}" inline-template>
                            <div class="form-group required {{ $errors->has('backup_type') ? 'has-error' : '' }}">
                                {!! Form::label('backup_type', trans('settings.backup_type'), array('class' => 'control-label')) !!}
                                <div class="controls">
                                    <select v-model="backup_type" name="backup_type" class="form-control">
                                        <option v-for="option in options" v-bind:value="option.id">
                                            @{{ option.text }}
                                        </option>
                                    </select>
                                    <span class="help-block">{{ $errors->first('backup_type', ':message') }}</span>
                                </div>
                            </div>

                            {{-- Dropbox --}}
                            <div v-if="backup_type=='dropbox'">
                                <div class="form-group required {{ $errors->has('disk_dbox_key') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_dbox_key', trans('settings.disk_dbox_key'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_dbox_key', old('disk_dbox_key', Settings::get('disk_dbox_key')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('disk_dbox_key', ':message') }}</span>
                                    </div>
                                </div>


                                <div class="form-group required {{ $errors->has('disk_dbox_secret') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_dbox_secret', trans('settings.disk_dbox_secret'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_dbox_secret', old('disk_dbox_secret', Settings::get('disk_dbox_secret')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('disk_dbox_secret', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="form-group required {{ $errors->has('disk_dbox_token') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_dbox_token', trans('settings.disk_dbox_token'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_dbox_token', old('disk_dbox_token', Settings::get('disk_dbox_token')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('disk_dbox_token', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="form-group required {{ $errors->has('disk_dbox_app') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_dbox_app', trans('settings.disk_dbox_app'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_dbox_app', old('disk_dbox_app', Settings::get('disk_dbox_app')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('disk_dbox_app', ':message') }}</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="backup_type=='s3'">
                                {{-- AWS S3 --}}
                                <div class="form-group required {{ $errors->has('disk_aws_key') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_aws_key', trans('settings.disk_aws_key'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_aws_key', old('disk_aws_key', Settings::get('disk_aws_key')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('disk_aws_key', ':message') }}</span>
                                    </div>
                                </div>

                                <div class="form-group required {{ $errors->has('disk_aws_secret') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_aws_secret', trans('settings.disk_aws_secret'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_aws_secret', old('disk_aws_secret', Settings::get('disk_aws_secret')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('disk_aws_secret', ':message') }}</span>
                                    </div>
                                </div>


                                <div class="form-group required {{ $errors->has('disk_aws_bucket') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_aws_bucket', trans('settings.disk_aws_bucket'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_aws_bucket', old('disk_aws_bucket', Settings::get('disk_aws_bucket')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('site_nbucket', ':message') }}</span>
                                    </div>
                                </div>


                                <div class="form-group required {{ $errors->has('disk_aws_region') ? 'has-error' : '' }}">
                                    {!! Form::label('disk_aws_region', trans('settings.disk_aws_region'), array('class' => 'control-label')) !!}
                                    <div class="controls">
                                        {!! Form::text('disk_aws_region', old('disk_aws_region', Settings::get('disk_aws_region')), array('class' => 'form-control')) !!}
                                        <span class="help-block">{{ $errors->first('site_nregion', ':message') }}</span>
                                    </div>
                                </div>
                            </div>
                        </backup-settings>
                    </div>

                </div>
            </div>
            <!-- Form Actions -->
            <div class="form-group">
                <div class="controls">
                    <button type="submit" class="btn btn-success"><i
                                class="fa fa-check-square-o"></i> {{trans('table.ok')}}</button>
                </div>
            </div>
            <!-- ./ form actions -->

            {!! Form::close() !!}
        </div>
    </div>
@stop
@section('scripts')
    <script type="text/javascript" src="{{ asset('js/icheck.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal-blue',
                radioClass: 'iradio_minimal-blue'
            });
            $("input[name='date_format']").on('ifChecked', function () {
                if ("date_format_custom_radio" != $(this).attr("id"))
                    $("input[name='date_format_custom']").val($(this).val()).siblings('.example').text($(this).siblings('span').text());
            });
            $("input[name='date_format_custom']").focus(function () {
                $("#date_format_custom_radio").attr("checked", "checked");
            });

            $("input[name='time_format']").on('ifChecked', function () {
                if ("time_format_custom_radio" != $(this).attr("id"))
                    $("input[name='time_format_custom']").val($(this).val()).siblings('.example').text($(this).siblings('span').text());
            });
            $("input[name='time_format_custom']").focus(function () {
                $("#time_format_custom_radio").attr("checked", "checked");
            });
            $("input[name='date_format_custom'], input[name='time_format_custom']").on('ifChecked', function () {
                var format = $(this);
                format.siblings('img').css('visibility', 'visible');
                $.post(ajaxurl, {
                    action: 'date_format_custom' == format.attr('name') ? 'date_format' : 'time_format',
                    date: format.val()
                }, function (d) {
                    format.siblings('img').css('visibility', 'hidden');
                    format.siblings('.example').text(d);
                });
            });
        });
        jQuery(document).ready(function($) {
            $('.smtp').hide();
            $('.email_driver').change(function () {
                if($(this).filter(':checked').val()=='smtp')
                {
                    $('.smtp').show();
                }
                else {
                    $('.smtp').hide();
                }
            });
            if($('.email_driver').filter(':checked').val()=='smtp')
            {
                $('.smtp').show();
            }

            $('.self_registration_role').hide();
            $('.self_registration').change(function () {
                if($(this).filter(':checked').val()=='yes')
                {
                    $('.self_registration_role').show();
                }
                else {
                    $('.self_registration_role').hide();
                }
            });
            if($('.self_registration').filter(':checked').val()=='yes')
            {
                $('.self_registration_role').show();
            }
        })
    </script>
@stop