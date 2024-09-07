<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class UpdateProductRequest extends FormRequest
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
            }
            return $rules;
        }else if($this->method() == 'PUT'){
            return [
//                'id'=>'required',
                'name'=>'required',
                'description'=>'required',
                'weight'=>'required',
                'dimensions'=>'required',
                'category_id'=>'required',
            ];
        }


    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response(["errors"=>$validator->errors()], 422));
    }
}
