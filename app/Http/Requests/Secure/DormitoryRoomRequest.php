<?php

namespace App\Http\Requests\Secure;

use App\Http\Requests\Request;

class DormitoryRoomRequest extends Request
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
            'title' => 'required|min:3',
            'dormitory_id' => 'required',
        ];
    }
}
