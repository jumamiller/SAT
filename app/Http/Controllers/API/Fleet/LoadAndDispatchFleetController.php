<?php

namespace App\Http\Controllers\API\Fleet;

use App\Http\Controllers\Controller;
use App\Http\Requests\FleetRequest;
use App\Models\Fleet;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function loadOrders(FleetRequest $fleetRequest)
    {
        try{
            $users=Fleet::with(['driver','driver.user','order'])
                ->paginate(5);
            return $this->success(true,'You have successfully retrieved fleet',
                $users,
                Response::HTTP_OK,
                'fleet','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
}
