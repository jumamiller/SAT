<?php

namespace App\Http\Controllers\API\Statistics;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Fleet;
use App\Models\Order;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class StatisticsController extends Controller
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try{
            //latest 5 orders
            $recent_orders=Order::with(['customer','customer.user'])
                ->latest('created_at')
                ->paginate(5);
            //latest 5 customers
            $recent_customers=Customer::with(['user'])->latest('created_at')->paginate(5);
            //get total pending orders
            $pending_orders=Order::where('status','PENDING')->count();
            //
            $total_loading_orders=Order::where('status','LOADING')->count();
            //Dispatched
            $total_dispatched_orders=Order::where('status','DISPATCHED')->count();
            //Delivered
            $total_delivered_orders=Order::where('status','DELIVERED')->count();
            //get available vehicles
            $available_vehicles=Fleet::where("status","AVAILABLE")->get()->count();
            //Loading
            $total_loading_vehicles=Fleet::where("status","LOADING")->get()->count();
            //
            $total_on_transit_vehicles=Fleet::where("status","ON_TRANSIT")->get()->count();
            //total customers
            $total_customers=Customer::all()
                ->count();
            //
            return $this->success(true,'You have successfully dashboard stats',
                [
                    'total_pending_orders'=>$pending_orders,
                    'total_loading_orders'=>$total_loading_orders,
                    'total_dispatched_orders'=>$total_dispatched_orders,
                    'total_delivered_orders'=>$total_delivered_orders,
                    //
                    'total_available_vehicles'=>$available_vehicles,
                    'total_loading_vehicles'=>$total_loading_vehicles,
                    'total_on_transit_vehicles'=>$total_on_transit_vehicles,
                    //
                    'total_customers'=>$total_customers,
                    'recent_orders'=>$recent_orders,
                    'recent_customers'=>$recent_customers,
                    //
                ],
                Response::HTTP_OK,
                'stats','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
