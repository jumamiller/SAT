<?php

namespace App\Http\Controllers\API\Fleet;

use App\Http\Controllers\Controller;
use App\Http\Requests\FleetRequest;
use App\Models\Fleet;
use App\Models\Order;
use App\Notifications\DispatchedOrderNotification;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LoadAndDispatchFleetController extends Controller
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
     * @param FleetRequest $fleetRequest
     * @return JsonResponse
     */
    public function loadVehicle(FleetRequest $fleetRequest)
    {
        try{
           return DB::transaction(function () use ($fleetRequest){
               $validated=$fleetRequest->validated();
               //check if order with such ID exists;
               $order=Order::where('order_number',$validated['order_number'])->first();
               //check if the fleet with such ID exists and is available
               $fleet=Fleet::where('id',$validated['fleet_id'])
                   ->AndWhere('status','AVAILABLE')
                   ->first();
               //
               if ($order==null || $fleet==null) {
                   return $this->error(false,
                       "Sorry, we could not process your request at this moment. Try again later.",
                       Response::HTTP_NOT_FOUND,
                       '',''
                   );
               }
               //update order
               $order->update([
                   'fleet_id'   =>$validated['fleet_id'],
                   'status'     =>'LOADING'
               ]);
               //Update fleet
               $fleet->update([
                   'status' =>'LOADING'
               ]);
               //Return success
               return $this->success(
                   true,
                   'You have successfully loaded the vehicle.',
                   $fleet,
                   Response::HTTP_CREATED
               );
           });
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * @param FleetRequest $fleetRequest
     * @return JsonResponse|mixed
     */
    public function dispatchVehicle(FleetRequest $fleetRequest)
    {
        try{
            return DB::transaction(function () use ($fleetRequest){
                $validated=$fleetRequest->validated();
                //check if the fleet with such ID exists and has been loaded
                $fleet=Fleet::where('id',$validated['fleet_id'])
                    ->AndWhere('status','LOADING')
                    ->first();
                //
                if ($fleet==null) {
                    return $this->error(false,
                        "Sorry, we could not process your request at this moment. Try again later.",
                        Response::HTTP_NOT_FOUND,
                        '',''
                    );
                }
                //get all orders with
                $orders=Order::where('fleet_id',$validated['fleet_id'])
                    ->with(['customer','customer.user'])
                    ->get();
                //loop orders and send notification
                foreach ($orders as $order){
                    //update order status
                    $order->update(['status'=>'DISPATCHED']);
                    //send notification
                    $order->customer->user->notify(new DispatchedOrderNotification($order->customer->user,$fleet));
                }
                //Update fleet
                $fleet->update([
                    'status' =>'ON_TRANSIT'
                ]);
                //Return success
                return $this->success(
                    true,
                    'You have successfully dispatched the vehicle.',
                    $fleet,
                    Response::HTTP_CREATED
                );
            });
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
}
