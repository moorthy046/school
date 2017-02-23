<?php namespace App\Http\Requests\Secure;

use Illuminate\Foundation\Http\FormRequest;

class MessageRequest  extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch($this->method())
        {
            case 'GET':
            case 'DELETE':
            {
                return [];
            }
            case 'POST':
            {
                return [
                    "user_id_receiver" => 'required',
                    'title' => 'required|min:3',
                    "content" => 'required|min:3',
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    "content" => 'required|min:3',
                ];
            }
            default:break;
        }
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
