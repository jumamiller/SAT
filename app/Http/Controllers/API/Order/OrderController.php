<?php

namespace App\Http\Controllers\API\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\FleetRequest;
use App\Http\Requests\OrderRequest;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Fleet;
use App\Models\Order;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    use ApiResponder;
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->hasRole('ADMIN')) {
//                abort(403, 'Unauthorized action.');
                return $this->error(false,
                    "You do not have enough permissions to access this resource",
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
            $users=Order::with(['orderHistories','customer','customer.user','fleet','fleet.driver','fleet.driver.user'])
                ->paginate(50);
            return $this->success(true,'You have successfully retrieved orders',
                $users,
                Response::HTTP_OK,
                'orders','');
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
    public function store(OrderRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                //check if customer with such ID exists;
                $customer=Customer::find($validated['customer_id']);
                //check if fleet exists
                $fleet=Fleet::find($validated['fleet_id']);
                //check
                if ($customer==null || $fleet==null) {
                    return $this->error(false,
                        "We do not have such customer/fleet at Solutech. Kindly register an account to proceed",
                        Response::HTTP_NOT_FOUND,
                        '',''
                    );
                }
                //generate order number
                $validated['order_number']=base64_encode(openssl_random_pseudo_bytes(3 * (22 >> 2)));
                //save
                $order=Order::create($validated);
                //return response
                return $this->success(
                    true,
                    'You have successfully submitted a new order request on '.config('app.name.'),
                    $order,
                    Response::HTTP_CREATED);
            });

        } catch (Exception $exception) {
            return
                $this->error(false,$exception->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR,
                    $exception->getCode(),$exception->getTrace());
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
            $users=Order::with(['orderHistories','customer','customer.user','fleet','fleet.driver','fleet.driver.user'])
                ->where('id',$id)
                ->first();
            return $this->success(true,'You have successfully retrieved the order details',
                $users,
                Response::HTTP_OK,
                'order details','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        try{
            //only update fields sent with values
            $fleet=Order::where('id',$id)
                ->update(array_filter($request->all()));
            //
            return $this->success(true,'You have successfully updated the order details',
                $fleet,
                Response::HTTP_OK,
                'order update','');
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
            $users=Order::where('id',$id)
                ->delete();
            return $this->success(true,'You have successfully deleted the order details',
                $users,
                Response::HTTP_OK,
                'Order deleted','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
}
