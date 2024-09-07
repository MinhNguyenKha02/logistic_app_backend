<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateInventoryRequest extends FormRequest
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
        if($this->method() == 'PATCH'){
            $rules = [];
            $fields = array_keys($this->all());
            foreach ($fields as $field) {
                $rules[$field] = "required";
//                if($field=='id')
//                    $rules[$field] = "required";

                if($field=='quantity')
                    $rules['quantity'] = "required|numeric|max:1000";
            }
            return $rules;
        }else if($this->method() == 'PUT'){
            return [
//                'id'=>'required',
                'warehouse_id'=>'required',
                'product_id'=>'required',
                'quantity'=>'required|numeric|max:1000',
                'unit'=>'required',
            ];
        }

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response(['errors'=>$validator->errors()], 422));
    }
}
