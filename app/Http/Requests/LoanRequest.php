<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class LoanRequest extends FormRequest
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
        if ($this->routeIs('AwardLoan')) {
            return self::awardLoan();
        } else if ($this->routeIs('RepayLoad')) {
            return self::repayLoan();
        } else {
            //
        }
    }
    public function awardLoan()
    {
        return [
            'account_id'        =>'required',
            'principal'         =>'required|numeric',
            'interest_rate'     =>'required|numeric',
            'loan_term'         =>'required|numeric',
            'repayment_frequency'=>'required'
        ];
    }
    public function repayLoan()
    {
        return [
            'user_id'   =>'required',
            'amount'    =>'required'
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
