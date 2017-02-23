<?php namespace App\Http\Requests\Secure;

use App\Models\ParentStudent;
use App\Models\User;
use Efriandika\LaravelSettings\Facades\Settings;
use Illuminate\Foundation\Http\FormRequest;

class ParentRequest extends FormRequest
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
                    'first_name' => 'required|min:3',
                    'last_name' => 'required|min:3',
                    'email' => 'required|email|unique:users,email',
                    'address' => 'required',
                    'gender' => 'required',
                    'birth_date' => 'required|date_format:"'.Settings::get('date_format').'"',
                    "birth_city" => 'required',
                    'password' => 'required|min:6',
                ];
            }
            case 'PUT':
            case 'PATCH':
            {
                return [
                    'first_name' => 'required|min:3',
                    'last_name' => 'required|min:3',
                    'address' => 'required',
                    'gender' => 'required',
                    'birth_date' => 'required|date_format:"'.Settings::get('date_format').'"',
                    "birth_city" => 'required',
                    'password' => 'min:6',
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
