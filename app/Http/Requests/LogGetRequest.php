<?php

namespace App\Http\Requests;


use Illuminate\Foundation\Http\FormRequest;

class LogGetRequest extends FormRequest
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
        return [];
    }

    /**
     * Sanitize route parameters.
     *
     * @return array
     */
    public function all($keys = null) 
    {
       $params = parent::all($keys);
       return [
           'order' => isset($params['order']) ? $params['order'] : 'asc',         // 'desc' | 'asc' order direction;
           'orderBy' => isset($params['orderBy']) ? $params['orderBy'] : 'id',    // order by column
           'size' => isset($params['size']) ? (integer) $params['size'] : 15,     // items per page
       ];
    }
}
