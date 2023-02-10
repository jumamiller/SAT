<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\AccountCreation;
use App\Traits\ApiResponder;
use Exception;
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
                return $this->success(true, 'You have successfully created an account with '.config('app.name.'),[],Response::HTTP_CREATED);
            });

        } catch (Exception $exception) {
            return
                $this->error(false,$exception->getMessage(),Response::HTTP_INTERNAL_SERVER_ERROR,$exception->getCode(),$exception->getTrace());
        }
    }
}
