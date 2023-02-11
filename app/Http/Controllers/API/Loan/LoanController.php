<?php

namespace App\Http\Controllers\API\Loan;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanRequest;
use App\Models\Account;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\LoanAwardNotification;
use App\Traits\ApiResponder;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class LoanController extends Controller
{
    use ApiResponder;

    /**
     * Award Loan
     * @param LoanRequest $request
     * @return JsonResponse|mixed
     */
    public function awardLoan(LoanRequest $request): mixed
    {
        try{
            //only allow admin here
            if (Auth::user()->hasRole('CLIENT')) {
                return $this->error(false,'Sorry,only Loan officers at Pezesha can process loans', ResponseAlias::HTTP_FORBIDDEN);
            }
            return DB::transaction(function () use ($request){
                //validated
                $validated=$request->validated();
                //if Account ID==null
                $account=Account::find($validated['account_id']);
                if ($account==null) {
                    return $this->error(false,'Sorry, no such account is registered with Pezesha.', ResponseAlias::HTTP_FORBIDDEN);
                }
                //account
                $toAccount=Account::where('id',$validated['account_id'])
                    ->first();
                //check if the recipient exist
                $user=User::find($toAccount->user_id);
                //if user ID==null || admin tries to send to themselves
                if ($user==null || $user->id==Auth::id()) {
                    return $this->error(false,'Sorry, loan request could not be processed at this moment.', ResponseAlias::HTTP_FORBIDDEN);
                }
                //create
                $loan=Loan::create($validated);
                //update account balance
                Account::where('id',$toAccount->id)
                    ->update([
                        'account_balance'=>$toAccount->account_balance+$validated['principal']
                    ]);
                //save transaction
                $transaction=Transaction::create([
                    'account_id'        =>$toAccount->id,
                    'transaction_code'  => base64_encode(openssl_random_pseudo_bytes(3 * (20 >> 2))),
                    'amount'            =>$validated['principal'],
                    'sender_account_number'=>'541479',//deposited by
                    'receiver_account_number'=>$toAccount->account_number,
                    'transaction_reference' =>'Pezesha Loan',
                    'status'=>'SUCCESS'
                ]);
                //update the recipient account
                $recipient=User::where('id',$validated['account_id'])->first();
                //notify
                $recipient->notify(new LoanAwardNotification($recipient,$transaction));
                //generate Loan Repayment schedule
                self::generateLoanRepaymentSchedule($loan);
                //
                return $this->success(true,
                    'You have successfully awarded '.$recipient->username.' loan amount KES '.$validated['principal'],
                    $transaction,
                    ResponseAlias::HTTP_CREATED
                );

            });
        } catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * Generate Loan
     * @return JsonResponse|void
     */
    public function generateLoanRepaymentSchedule($loan)
    {
        // Loan amount
        $loan_amount = $loan->principal;

        // Number of months
        $months = $loan->loan_term;

        // Annual interest rate
        $annual_interest_rate = $loan->interest_rate;

        // Convert to monthly interest rate
        $monthly_interest_rate = $annual_interest_rate / 12;

        // Payment frequency
        $frequency = $loan->repayment_frequency; // options: weekly, bi-monthly, monthly
        //

        // Calculate number of payments based on frequency
        $payments = $months;
        switch ($frequency) {
            case 'WEEKLY':
                $payments = $months * 4;
                break;
            case 'BI_MONTHLY':
                $payments = $months /2;
                break;
            case 'MONTHLY':
                $payments = $months;
                break;
        }
        // Calculate monthly payment
        $monthly_payment = $loan_amount * ($monthly_interest_rate / (1 - pow(1 + $monthly_interest_rate, -$payments)));

        // Repayment schedule
        $repayment_schedule = array();
        $remaining_balance = $loan_amount;
        for ($i = 0; $i < $payments; $i++) {
            $interest = $remaining_balance * $monthly_interest_rate;
            $principal = $monthly_payment - $interest;
            $remaining_balance = $remaining_balance - $principal;
            $repayment_schedule[$i] = array(
                'payment' => $i + 1,
                'interest' => $interest,
                'principal' => $principal,
                'remaining_balance' => $remaining_balance
            );
        }

        //
        try{
            foreach ($repayment_schedule as $payment) {
                $schedule=LoanRepayment::create([
                    'loan_id' => $loan->id,
                    'months' => $loan->months,
                    'payment_number' => $payment['payment'],
                    'interest' => $payment['interest'],
                    'principal' => $payment['principal'],
                    'remaining_balance' => $payment['remaining_balance']
                ]);
            }
        }
        catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }

    /**
     * @param LoanRequest $request
     * @return JsonResponse|mixed
     */
    public function repayLoan(LoanRequest $request)
    {
        try{
            //
            return DB::transaction(function() use ($request){
                $validated=$request->validated();
                //record the loan repaid
            });

        }catch (Exception $exception) {
            return $this->error(false,$exception->getMessage());
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
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
     * @return Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
