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
        if ($this->routeIs('Dispatch')) {
            return self::dispatchVehicle();
        } else if ($this->routeIs('LoadingFleet')) {
            return self::loadingOrders();
        } else {
            return self::fleet();
        }
    }

    /**
     * @return string[]
     */
    private function fleet(): array
    {
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

    /**
     * @return string[]
     */
    private function loadingOrders(): array
    {
        return [
            'order_number'  =>'required',
            'fleet_id'      =>'required',
        ];
    }
    /**
     * @return string[]
     */
    private function dispatchVehicle(): array
    {
        return [
            'fleet_id'      =>'required',
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
