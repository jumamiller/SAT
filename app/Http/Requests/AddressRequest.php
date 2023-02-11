<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class AddressRequest extends FormRequest
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
            'customer_id' =>'required|numeric',
            'country'       =>'required',
            'county'        =>'required',
            'sub_county'    =>'required',
            'location'      =>'required',
            'sub_location'  =>'required',
            'village'       =>'required',
            'building'      =>'required',
            'landmark'      =>'required',
            'status'        =>'required'
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
            'meta'      =>'Some missing/invalid required data input'
        ], Response::HTTP_BAD_REQUEST));
    }
}
