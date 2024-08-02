<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateReturnOrderRequest extends FormRequest
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
        if($this->method() == 'PATCH') {
            $rules = [];
            $fields = array_keys($this->all());
            foreach ($fields as $field) {
                $rules[$field] = "required";
                if($field=='date'){
                    $rules[$field] = 'date|required';
                }
            }
            return $rules;
        }else if($this->method() == 'PUT') {
            return [
                'id'=>'required',
                'customer_id'=>'required',
                'product_id'=>'required',
                'date'=>'required|date',
                'reason'=>'required',
                'status'=>'required'
            ];
        }

    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response($validator->errors(), 422));
    }
}
