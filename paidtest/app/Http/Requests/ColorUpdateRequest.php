<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ColorUpdateRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'taxi_id' => 'required|exists:taxis,id',
            'color_id' => 'required|exists:colors,id'
        ];
    }
}
