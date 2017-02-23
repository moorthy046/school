<?php

namespace App\Http\Controllers\Secure;

use App\Helpers\Thumbnail;
use App\Http\Requests\Secure\SettingRequest;
use App\Models\Option;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Support\Str;
use Session;

class SettingController extends SecureController
{
    public function __construct()
    {
        parent::__construct();

        view()->share('type', 'setting');
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $title = trans('settings.settings');
        $max_upload_file_size = array(
            '1000' => '1MB',
            '2000' => '2MB',
            '3000' => '3MB',
            '4000' => '4MB',
            '5000' => '5MB',
            '6000' => '6MB',
            '7000' => '7MB',
            '8000' => '8MB',
            '9000' => '9MB',
            '10000' => '10MB',
        );
        $options = Option::all()->flatten()->groupBy('category')->map(function ($grp) {
            return $grp->lists('value', 'title');
        });

        $opts = Option::all()->flatten()->groupBy('category')->map(function ($grp) {
            return $grp->map(function ($opt) {
                return [
                    'text' => $opt->value,
                    'id' => $opt->title
                ];
            })->values();
        });
        $self_registration_role = array(
            'student' => 'Student',
            'visitor' => 'Visitor',
        );

        return view('setting.index', compact('title', 'max_upload_file_size', 'options', 'opts', 'self_registration_role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param SettingRequest|Request $request
     * @param Setting $setting
     * @return \Illuminate\Http\Response
     * @internal param int $id
     */
    public function store(SettingRequest $request)
    {
        if ($request->hasFile('logo_file')) {
            $file = $request->file('logo_file');
            $extension = $file->getClientOriginalExtension();
            $picture = str_random(10) . '.' . $extension;

            $destinationPath = public_path() . '/uploads/site/';
            $file->move($destinationPath, $picture);
            Thumbnail::generate_image_thumbnail($destinationPath . $picture, $destinationPath . 'thumb_' . $picture);

            $request->merge(['logo' => $picture]);
        }
        if ($request->hasFile('visitor_card_background_file') != "") {
            $file = $request->file('visitor_card_background_file');
            $extension = $file->getClientOriginalExtension();
            $picture = str_random(10) . '.' . $extension;

            $destinationPath = public_path() . '/uploads/visitor_card/';
            $file->move($destinationPath, $picture);

            $request->merge(['visitor_card_background' => $picture]);
        }

        $request->date_format = $request->date_format_custom;
        $request->time_format = $request->time_format_custom;
        if ($request->date_format == "") {
            $request->date_format = 'd.m.Y';
        }
        if ($request->time_format == "") {
            $request->time_format = 'H:i';
        }
        $request->merge([
            'jquery_date' => $this->dateformat_PHP_to_jQueryUI($request->date_format),
            'jquery_date_time' => $this->dateformat_PHP_to_jQueryUI($request->date_format . ' ' . $request->time_format)
        ]);
        foreach ($request->except('_token', 'logo_file', 'date_format_custom', 'time_format_custom', 'visitor_card_background_file')
                 as $key => $value) {
            Settings::set($key, $value);
        }

        return redirect()->back();
    }


}
