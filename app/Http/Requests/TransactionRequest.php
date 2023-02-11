<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class TransactionRequest extends FormRequest
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
        if ($this->routeIs('TransferFunds')) {
            return self::transferFunds();
        }
        else  {
            return self::deposit();
        }
    }
    public function transferFunds(): array
    {
        return [
            'account_id'            =>'required',//sender account ID
            'amount'                =>'required',
            'sender_account_number' =>'required',
            'receiver_account_number'=>'required',
        ];
    }
    public function deposit(): array
    {
        return [
            'account_id'            =>'required',
            'amount'                =>'required',
            'account_number'        =>'required',
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
