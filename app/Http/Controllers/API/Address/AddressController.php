<?php

namespace App\Http\Controllers\API\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    use ApiResponder;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param AddressRequest $request
     * @return JsonResponse
     */
    public function store(AddressRequest $request)
    {
        //
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                //
                $user=Address::create($validated);
                //return response
                return $this->success(
                    true,
                    'You have successfully added your address details '.config('app.name.'),
                    $user,
                    Response::HTTP_CREATED);
            });

        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
