<?php
namespace App\Http\Requests\Secure;

use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;


class AddAttendanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'hour' => 'required',
            'date' => 'required|date_format:"'.Settings::get('date_format').'"',
            'students' => "required"
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}