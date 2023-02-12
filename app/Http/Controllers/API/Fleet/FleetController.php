<?php

namespace App\Http\Controllers\API\Fleet;

use App\Http\Controllers\Controller;
use App\Http\Requests\FleetRequest;
use App\Models\Driver;
use App\Models\Fleet;
use App\Notifications\DispatchedOrderNotification;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class FleetController extends Controller
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
            $fleet=Fleet::with(['driver','driver.user','orders','orders.customer','orders.customer.user'])
                ->paginate(50);
            return $this->success(true,'You have successfully retrieved fleet',
                $fleet,
                Response::HTTP_OK,
                'fleet','');
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
    public function store(FleetRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                //check if driver with such ID exists;
                $driver=Driver::find($validated['driver_id']);
                if ($driver==null) {
                    return $this->error(false,
                        "We do not have such driver at Solutech. Kindly register an account to proceed",
                        Response::HTTP_NOT_FOUND,
                        '',''
                    );
                }
                $fleet=Fleet::create($validated);
                //return response
                return $this->success(
                    true,
                    'You have successfully added new fleet '.config('app.name.'),
                    $fleet,
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
            $fleet=Fleet::with(['driver','driver.user','orders','orders.customer','orders.customer.user'])
                ->where('id',$id)
                ->first();
            return $this->success(true,'You have successfully retrieved the fleet details',
                $fleet,
                Response::HTTP_OK,
                'fleet details','');
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
            $fleet=Fleet::with('orders')
                ->where('id',$id)
                ->first();
            //
            if ($request->input('status')==='LOADING') {
                //orders are being loaded
                //update order statuses to load
                foreach ($fleet->orders as $order){
                    //update order status
                    //check if order has already been dispatched
                    if ($order->status!=='DELIVERED') {
                        $order->update(['status'=>$request->input('status')]);
                    }
                }
                //update the fleet status to loading
                $fleet->update(['status'=>$request->input('status')]);

            } else if ($request->input('status')==='ON_TRANSIT') {
                //orders dispatched
                //orders are being loaded
                $fleet=Fleet::with('orders')
                    ->where('id',$id)
                    ->first();
                //update order statuses to load
                foreach ($fleet->orders as $order){
                    //update order status
                    if ($order->status!=='DELIVERED') {
                        $order->update(['status'=>'DISPATCHED']);
                        //send email notification
                        $order->customer->user->notify(new DispatchedOrderNotification($order->customer->user,$order));
                    }
                }
                //update the fleet status to On Transit
                $fleet->update(['status'=>$request->input('status')]);
            } else if ($request->input('status')==='AVAILABLE'){
                //Order delivered
                //update order statuses to load
                foreach ($fleet->orders as $order){
                    //update order status
                    $order->update(['status'=>'DELIVERED']);
                }
                //update the fleet status to On Transit
                $fleet->update(['status'=>$request->input('status')]);
            } else {
                //only update fields sent with values
                $fleet=Fleet::where('id',$id)
                    ->update(array_filter($request->all()));
                //
                return $this->success(true,'You have successfully updated the fleet details',
                    $fleet,
                    Response::HTTP_OK,
                    'fleet update','');
            }
            return $this->success(true,'You have successfully updated the fleet details',
                $fleet,
                Response::HTTP_OK,
                'fleet update','');
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
            $users=Fleet::where('id',$id)
                ->delete();
            return $this->success(true,'You have successfully deleted the fleet',
                $users,
                Response::HTTP_OK,
                'Fleet deleted','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
}
