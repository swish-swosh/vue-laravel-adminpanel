<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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

    // handle formData type input before rules()
    protected function prepareForValidation()
    {
        // decode roles which will be a stringified array (send over by formData) and map ids
        // if ($this->has('roles'))
        //     $this->merge(['roles'=> json_decode($this->roles)]);

        // // decode boolean
        // if ($this->has('is_active'))
        //     $this->merge(['is_active'=> $this->is_active === 'true'? true:false]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'=>'required|max:55',
            'email' => 'required|email|unique:users', 
            'password' => 'required', 
            'c_password' => 'required|same:password', 
        ];
    }
}
