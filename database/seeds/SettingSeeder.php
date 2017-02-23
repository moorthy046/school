<?php

use Efriandika\LaravelSettings\Facades\Settings;

class SettingSeeder extends DatabaseSeeder {

    public function run()
    {
        Settings::set('logo', 'sms.png');
        Settings::set('name', 'SMS');
        Settings::set('payment_methods', 'Cash');
        Settings::set('currency', 'USD');
        Settings::set('backup_type', 'local');
        Settings::set('allowed_extensions_avatar', 'gif,jpg,jpeg,png');
        Settings::set('allowed_extensions_file', 'gif,jpg,jpeg,png,txt,pdf');
        Settings::set('max_upload_file_size', 2000);
        Settings::set('max_upload_avatar_size', 2000);
        Settings::set('date_format', 'Y-d-m');
        Settings::set('time_format', 'g:i a');
        Settings::set('jquery_date', 'GGGG-DD-MM');
        Settings::set('jquery_date_time', 'GGGG-DD-MM h:mm a');
        Settings::set('upload_webcam', 'upload');
    }

}