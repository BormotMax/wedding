<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAccessRequest extends FormRequest
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'roles' => 'sometimes|array',
            'roles.*' => 'required|exists:roles,id',
        ];
    }
}