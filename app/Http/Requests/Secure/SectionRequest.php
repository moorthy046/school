<?php namespace App\Http\Requests\Secure;

use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'section_teacher_id' => 'required',
            'title' => 'required|min:3',
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
