<?php

namespace App\Http\Requests;

use Symfony\Component\Console\Input\Input;
use Illuminate\Foundation\Http\FormRequest;

class FileUploadRequest extends FormRequest
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
            // 'filenames' => 'required',
            // 'filenames.*' => 'mimes:pdf,docx,zip, png, jpg'
        ];
    }
}
