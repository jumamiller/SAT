<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\AccountCreation;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    use ApiResponder;
    public function register(UserRequest $registerRequest)
    {
        try{
            return DB::transaction(function () use ($registerRequest) {
                //validated
                $validated=$registerRequest->validated();
                $validated['password']=Hash::make($validated['password']);

                $user=User::create($validated);
                //
                $user->assignRole('CLIENT');
                //notify
                $user->notify(new AccountCreation($user));
                //return response
                return $this->success(
                    true,
                    'You have successfully created an account with '.config('app.name.'),
                    $user,
                    Response::HTTP_CREATED);
            });

        } catch (Exception $exception) {
            return
                $this->error(false,$exception->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR,$exception->getCode(),$exception->getTrace());
        }
    }

    /**
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        try{
            //get credentials
            $credentials=$request->validated();
            if (Auth::attempt($credentials)) {

                return response()
                    ->json([
                        'success' =>true,
                        'message' =>'You have successfully logged in',
                        'data'    =>Auth::user(),
                        'token'   =>Auth::user()->createToken("Solutech")->accessToken
                    ]);
            }
            else
            {
                return $this->error(false,'Wrong login credentials',Response::HTTP_BAD_REQUEST);
            }
        } catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
}
