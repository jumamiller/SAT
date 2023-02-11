<?php

namespace App\Http\Controllers\API\Transaction;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionRequest;
use App\Models\Account;
use App\Models\KYC;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\DepositFundsNotification;
use App\Notifications\RecipientTransactionNotification;
use App\Notifications\SenderTransactionNotification;
use App\Shared\UploadBase64Image;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TransactionController extends Controller
{
    use ApiResponder;

    /**
     * @param TransactionRequest $request
     * @return JsonResponse|mixed
     */
    public function deposit(TransactionRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                //
                $toAccount=Account::where('id',$validated['account_id'])
                    ->orWhere('account_number',$validated['account_number'])
                    ->lockForUpdate()
                    ->first();
                //update locked account
                Account::where('id',$toAccount->id)
                    ->update([
                        'account_balance'=>$toAccount->account_balance+$validated['amount']
                    ]);
                //record transaction
                //record transaction
                $transaction=Transaction::create([
                    'account_id'        =>$toAccount->id,
                    'transaction_code'  => base64_encode(openssl_random_pseudo_bytes(3 * (20 >> 2))),
                    'amount'            =>$validated['amount'],
                    'sender_account_number'=>$toAccount->account_number,
                    'receiver_account_number'=>$toAccount->account_number,
                    'transaction_reference' =>'Fund deposited to '.$toAccount->account_name,
                    'status'=>'SUCCESS'
                ]);

                // user
                $depositedBy=User::where('id',$toAccount->user_id)
                    ->first();
                $depositedBy->notify(new DepositFundsNotification($depositedBy,$transaction));

                //return response
                return $this->success(
                    true,
                    'You have successfully deposited money into  your account ',
                    $transaction,
                    Response::HTTP_CREATED);
            });

        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Transfer funds
     * @param TransactionRequest $request
     * @return JsonResponse|mixed
     */
    public function transferFunds(TransactionRequest $request)
    {
        try{
            return DB::transaction(function () use ($request) {
                //validated
                $validated=$request->validated();
                //transaction from which account(LOCK)
                $fromAccount=Account::where('id',$validated['account_id'])
                    ->orWhere('account_number',$validated['sender_account_number'])
                    ->lockForUpdate()
                    ->first();
                //transaction to which account (LOCK)
                $toAccount = DB::table('accounts')
                    ->where('account_number', $validated['receiver_account_number'])
                    ->lockForUpdate()
                    ->first();
                //query for sufficient balance
                // Check if there are sufficient funds in the source account
                if ($fromAccount->account_balance < $validated['amount']) {
                    return $this->error(false,'You have insufficient balance to complete this transaction');
                }
                //update the account balance from the sender (source account)
                Account::where('id',$validated['account_id'])
                    ->orWhere('account_number',$validated['sender_account_number'])
                    ->update([
                        'account_balance'=>$fromAccount->account_balance-$validated['amount']
                    ]);
                //update the account balance for the recipient account
                Account::where('account_number',$validated['receiver_account_number'])
                    ->update([
                        'account_balance'=>$toAccount->account_balance+$validated['amount']
                    ]);
                //record transaction
                $transaction=Transaction::create([
                    'account_id'        =>$fromAccount->id,
                    'transaction_code'  => base64_encode(openssl_random_pseudo_bytes(3 * (20 >> 2))),
                    'amount'            =>$validated['amount'],
                    'sender_account_number'=>$fromAccount->account_number,
                    'receiver_account_number'=>$toAccount->account_number,
                    'transaction_reference' =>'Fund Transfer from '.$fromAccount->account_name,
                    'status'=>'SUCCESS'
                ]);
                //send notification to sender and recipient
                $sender=User::where('id',$fromAccount->user_id)->first();
                $recipient=User::where('id',$toAccount->user_id)->first();
                //send to sender
                $sender->notify(new SenderTransactionNotification($recipient,$sender,$transaction));
                //notify recipient
                $recipient->notify(new RecipientTransactionNotification($recipient,$sender,$transaction));

                //return response
                return $this->success(
                    true,
                    'You have successfully transferred  funds '.config('app.name.'),
                    $transaction,
                    Response::HTTP_CREATED);
            });

        }catch (Exception $exception) {
            DB::rollBack();
            return $this->error(false,$exception->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return void
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
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
