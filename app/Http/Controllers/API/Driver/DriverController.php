<?php

namespace App\Http\Controllers\API\Driver;

use App\Http\Controllers\Controller;
use App\Http\Requests\DriverRequest;
use App\Http\Requests\FleetRequest;
use App\Models\Driver;
use App\Models\Fleet;
use App\Models\User;
use App\Shared\UploadBase64Image;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class DriverController extends Controller
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
            $users=Driver::with(['user','fleet'])
                ->paginate(5);
            return $this->success(true,'You have successfully retrieved drivers',
                $users,
                Response::HTTP_OK,
                'drivers','');
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
    public function store(DriverRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                $validated['license_file_path']=UploadBase64Image::upload_image($validated['license_file_path'],'driver');
                //check if user with such ID exists;
                $user=User::find($validated['user_id']);
                if ($user==null) {
                    return $this->error(false,
                        "We do not have such user at Solutech. Kindly register an account to proceed",
                        Response::HTTP_NOT_FOUND,
                        '',''
                    );
                }
                $fleet=Driver::create($validated);
                //return response
                return $this->success(
                    true,
                    'You have successfully added new driver on '.config('app.name.'),
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
            $users=Driver::with(['driver'])
                ->where('id',$id)
                ->first();
            return $this->success(true,'You have successfully retrieved the driver details',
                $users,
                Response::HTTP_OK,
                'driver details','');
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
            $fleet=Driver::where('id',$id)
                ->update(array_filter($request->all()));
            //
            return $this->success(true,'You have successfully updated the driver details',
                $fleet,
                Response::HTTP_OK,
                'driver update','');
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
            $users=Driver::where('id',$id)
                ->delete();
            return $this->success(true,'You have successfully deleted the driver',
                $users,
                Response::HTTP_OK,
                'Driver deleted','');
        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
}
