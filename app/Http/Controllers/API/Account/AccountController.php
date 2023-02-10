<?php

namespace App\Http\Controllers\API\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\AccountRequest;
use App\Models\Account;
use App\Models\Address;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
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
     * @param AccountRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(AccountRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                $validated['account_number']=mt_rand(11111111111,999999999999999999);
                $validated['account_balance']=0;
                $validated['account_limit']=20000;
                $validated['status']='ACTIVE';
                //
                $user=Account::create($validated);
                //return response
                return $this->success(
                    true,
                    'You have successfully added your account details '.config('app.name.'),
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
     * @return void
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
