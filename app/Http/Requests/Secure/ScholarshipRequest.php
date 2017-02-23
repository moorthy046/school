<?php

namespace App\Http\Requests\Secure;

use App\Http\Requests\Request;
use Efriandika\LaravelSettings\Facades\Settings;

class ScholarshipRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'user_id' => 'required',
            'name' => 'required',
        ];
    }
}
