<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreVehicleRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'id'=>'required|unique:vehicles,id',
            'carrier_id'=>'required',
            'type'=>'required',
            'longitude'=>'required',
            'latitude'=>'required'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response($validator->errors(), 422));
    }
}
