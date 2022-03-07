<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Services\RegisterService;
use App\Services\SafeMemoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Traits\ResponseAPI;
use App\Services\SmsMessageOutService;
use Exception;


class SafeMemoController extends Controller
{
    use ResponseAPI;
    public $successStatus = 200;
    protected $company_service;

    public function __construct()
    {
        $this->register_service = new RegisterService;
        $this->safe_memo_service = new SafeMemoService;
        $this->sms_messageout_service = new SmsMessageOutService;
    }

    public function getSafeMemo(){
        $safe_memos = $this->safe_memo_service->getData();
        $data =[];
        if(count($safe_memos) != 0 && $safe_memos != null){
            foreach($safe_memos as $index=>$safe_memo){
                $user = $this->register_service->getUserByAdmin($safe_memo->user_id);
                $data[$index]=[
                    "uu_id" => $safe_memo->uu_id,
                    "user_id" => isset($user->uu_id) ? $user->uu_id : null,
                    "reason" => $safe_memo->reason,
                    "safe_spam" => $safe_memo->safe_spam,
                    "entry_by_id" => $safe_memo->entry_by_id,
                    "entry_by_nick_name" => $safe_memo->entry_by_nick_name,
                    "followup" => $safe_memo->followup,
                    "approve_reason" => $safe_memo->approve_reason,
                    "approve_for" => $safe_memo->approve_for,
                    "ip_address" => $safe_memo->ip_address,
                    "created_at" => $safe_memo->created_at,
                    "updated_at" => $safe_memo->updated_at,
                ];
            } 

            return $this->success($data);
        }
        else{
            return $this->error('Data not found',404);
        }
    }

    public function createSafeMemo(Request $request){
        $user = $this->register_service->findByUUID($request->user_id);
        $data = [
            'user_id'=> isset($user->id) ? $user->id : null,
            'reason'=> isset($request->reason) ? $request->reason : null,
            'safe_spam'=> isset($request->safe_spam) ? $request->safe_spam : null,
            'entry_by_id'=> isset($request->entry_by_id) ? $request->entry_by_id : null,
            'entry_by_nick_name'=> isset($request->entry_by_nick_name) ? $request->entry_by_nick_name : null,
            'followup'=> isset($request->followup) ? $request->followup : null,
            'approve_reason'=> isset($request->approve_reason) ? $request->approve_reason : null,
            'approve_for'=> isset($request->approve_for) ? $request->approve_for : null,
            'ip_address'=>request()->ip(),
        ];
        try
        {
            $safe_memo = $this->safe_memo_service->store($data);
            $safe_memo['user_id'] = isset($user->uu_id) ? $user->uu_id : null;
            return $this->success($safe_memo);
        }
        catch(Exception $e)
        {
           return $this->error($e->getMessage(),'404');
        }
    }
    
    public function getSafeMemoById($id){
        $user_id = $this->register_service->findByUUID($id);
        if(!isset($user_id)){
            return $this->error('User not found',404);
        }
        $safe_memos = $this->safe_memo_service->show($user_id->id);
        if(count($safe_memos) != 0 && $safe_memos != null){
            $data=[];
            foreach($safe_memos as $index=>$safe_memo)
            {
                $user = $this->register_service->getUserByAdmin($safe_memo->user_id);
                $data[$index] = [
                    "user_id"=> isset($user->uu_id) ? $user->uu_id : null,
                    "user_name"=> isset($user->name) ? $user->name : null,
                    "reason"=> $safe_memo->reason,
                    "safe_spam"=> $safe_memo->safe_spam,
                    "entry_by_id"=> $safe_memo->entry_by_id,
                    "entry_by_nick_name"=> $safe_memo->entry_by_nick_name,
                    "followup"=> $safe_memo->followup,
                    "approve_reason"=> $safe_memo->approve_reason,
                    "approve_for"=> $safe_memo->approve_for,
                    "ip_address"=> $safe_memo->ip_address,
                    "created_at"=> $safe_memo->created_at,
                    "updated_at"=> $safe_memo->updated_at
                ];
            }
            return $this->success($data);
        }
        else{
            return $this->error('Data not found',404);
        }
    }

}
