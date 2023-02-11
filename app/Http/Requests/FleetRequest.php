<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class FleetRequest extends FormRequest
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
        //
        return [
            'driver_id'         =>'required|numeric',
            'name'              =>'required|min:3',
            'registration_number'=>'required',
            'model'             =>'required',
            'manufacturer'      =>'required',
            'year'              =>'required',
            'capacity'          =>'required',
            'status'            =>'required',
        ];
    }
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
