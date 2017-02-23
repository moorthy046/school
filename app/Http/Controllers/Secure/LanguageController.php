<?php namespace App\Http\Controllers\Secure;

use Session;

class LanguageController extends SecureController {

    public function setlang($language)
    {
        Session::set('language', $language);
        return redirect()->back();
    }

}