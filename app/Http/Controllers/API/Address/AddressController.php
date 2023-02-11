<?php

namespace App\Http\Controllers\API\Address;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\Customer;
use App\Models\User;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AddressController extends Controller
{
    use ApiResponder;

    /**
     * Protect controller
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->hasRole('ADMIN')) {
//                abort(403, 'Unauthorized action.');
                return $this->error(false,
                    "You do not have enough permissions to add customer address",
                    Response::HTTP_FORBIDDEN,
                    '',''
                );
            }

            return $next($request);
        });
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            $users=Address::with(['customer','customer.user'])
                ->paginate(5);
            return $this->success(true,'You have successfully retrieved the list of customer addresses',
                $users,
                Response::HTTP_OK,
                'addresses','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(AddressRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                //check if user with such ID exists;
                $customer=Customer::find($validated['customer_id']);
                if ($customer==null) {
                    return $this->error(false,
                        "We do not have such customer at Solutech. Kindly register an account to proceed",
                        Response::HTTP_NOT_FOUND,
                        '',''
                    );
                }
                $address=Address::create($validated);
                //return response
                return $this->success(
                    true,
                    'You have successfully added customer address '.config('app.name.'),
                    $address,
                    Response::HTTP_CREATED);
            });

        } catch (Exception $exception) {
            return
                $this->error(false,$exception->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR,$exception->getCode(),$exception->getTrace());
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
