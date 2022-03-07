<?php

namespace App\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Services\WalletService;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Events\CompanyActivity;
use App\Notifications\saveNotification;
use App\Traits\PusherTrait;
use Pusher\Pusher;

class WalletController extends Controller
{
    use ResponseAPI,PusherTrait;
    protected $user,$wallet_service,$company_service;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();
            return $next($request);
        });

        $this->wallet_service = new WalletService();
        $this->company_service = new CompanyService();
    }
    public function getCompanyWalletBalance(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(isset($company))
        {
            $company_wallet =[
                'walletBalance' => $company->balance,
            ];
            return $this->success( $company_wallet);
        }
        else
        {
            return $this->error("Company Not Found", 404);
        }
    }

    public function depositAmountToWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'companyId' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found", 404);
        }
        $amount = $request->amount;
        try
        {
            $previous_balance = $company->balance;
            $company->deposit($amount);
            $data = [
                'previousBalance' => $previous_balance,
                "amountDeposited" => $amount,
                "walletBalance" => $company->balance,
            ];
            $company_activities = $company;
            $company_activities['type'] = 'wallet';
            $company_activities['amount'] = $amount;
            $company_activities['status'] = 'deposit';
            event(new CompanyActivity($company));
            $message= $company->name." deposited Rs ".$request->amount." to wallet";
            $this->sendNotification($message);
            $company->notify(new saveNotification($message));
            return $this->success($data);

        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function withdrawAmountFromWallet(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'companyId' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company))
        {
            return $this->error("Company Not Found", 404);
        }
        $amount = $request->amount;
        try
        {
            $previous_balance = $company->balance;
            $company->withdraw($amount);
            $data = [
                'previousBalance' => $previous_balance,
                "amountWithdrawn" => $amount,
                "walletBalance" => $company->balance,
            ];
            $company_activities = $company;
            $company_activities['type'] = 'wallet';
            $company_activities['amount'] = $amount;
            $company_activities['status'] = 'withdraw';
            event(new CompanyActivity($company));
            $message= $company->name." withdrawn Rs ".$request->amount." from wallet";
            $this->sendNotification($message);
            $company->notify(new saveNotification($message));
            return $this->success($data);

        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function createWallet(Request $request)
    {

        if($this->user->hasWallet($request->walletName))
        {
            return $this->error('Wallet '.$request->walletName.' Already exist for you','404');
        }
        $wallet = $this->user->createWallet([
            'name' => $request->walletName,
            'slug' => $request->walletName,
        ]);
        $data = [
            'wallet' => $wallet->name,
            'balance' => $wallet->balance
        ];
        return $this->success($data);
    }



    public function walletFundTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fromWallet' => 'required',
            'toWallet' => 'required',
            'amount' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $from_wallet = $this->wallet_service->getWalletById($request->fromWallet);
        $to_wallet = $this->wallet_service->getWalletById($request->toWallet);
        $amount = $request->amount;
        if(!isset($from_wallet) || !isset($to_wallet))
        {
            return $this->error("This is not your wallet",500);
        }
        if($from_wallet->balance < $amount)
        {
            return $this->error("No sufficient fund in your wallet",500);
        }
        $from_wallet->transfer($to_wallet, $amount);
        $data = [
            'amountTransfered' => $amount,
            'from' => $from_wallet->name,
            'to' => $to_wallet->name,
            'fromWalletBalance' => $from_wallet->balance,
            'toWalletBalance' => $to_wallet->balance,
        ];
        return $this->success($data);
    }
}
