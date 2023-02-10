<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

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

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'first_name'    =>'required',
            'middle_name'   =>'required',
            'last_name'     =>'required',
            'username'      =>'required:unique:users',
            'email'         =>'required|email|unique:users',
            'phone_number'  =>'required|unique:users',
            'document_type'  =>'required',
            'document_number'=>'required',
            'nationality'   =>'required',
            'password'   =>'required|min:8|max:16',
        ];
    }
    /**
     * @param Validator $validator
     * @return void
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success'   =>false,
            'message'   =>$validator->errors()->first(),
            'data'      => [],
            'errors'    => $validator->errors(),
            'meta'      =>'some missing/invalid required data input'
        ], Response::HTTP_BAD_REQUEST));
    }
}
