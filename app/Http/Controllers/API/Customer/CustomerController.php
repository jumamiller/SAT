<?php

namespace App\Http\Controllers\API\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Models\User;
use App\Notifications\CustomerAccountCreatedNotification;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    use ApiResponder;
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
     * @return JsonResponse
     */
    public function index()
    {
        try{
            $users=Customer::with(['user','orders','orders.fleet','orders.fleet.driver','orders.fleet.driver.user','addresses'])
                ->paginate(5);
            return $this->success(true,'You have successfully retrieved the list of customers',
                $users,
                Response::HTTP_OK,
                'customers','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(CustomerRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                //check if user with such ID exists;
                $user=User::find($validated['user_id']);
                if ($user==null) {
                    return $this->error(false,
                        "We do not have such user at Solutech. Kindly register an account to proceed",
                        Response::HTTP_NOT_FOUND,
                        '',''
                    );
                }
                Customer::create($validated);
                //send notification to customer
//                $user->notify(new CustomerAccountCreatedNotification($user));//disable fr failures
                //return response
                return $this->success(
                    true,
                    'You have successfully added new customer '.config('app.name.'),
                    $user,
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
     * @return JsonResponse
     */
    public function show($id)
    {
        try{
            $customer=Customer::with(['user','orders','orders.fleet','orders.fleet.driver','orders.fleet.driver.user','addresses'])
                ->where('id',$id)
                ->first();
            return $this->success(true,'You have successfully retrieved the customer details',
                $customer,
                Response::HTTP_OK,
                'customer details','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(CustomerRequest $request, $id)
    {
        try{
            $users=Customer::where('id',$id)
                ->update([
                    //nothing to update
                ]);
            return $this->success(true,'You have successfully updated the customer details',
                $users,
                Response::HTTP_OK,
                'customer update','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function destroy($id)
    {
        try{
            $customer=Customer::where('id',$id)
                ->delete();
            return $this->success(true,'You have successfully deleted the customer details',
                $customer,
                Response::HTTP_OK,
                'customer deleted','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
}
