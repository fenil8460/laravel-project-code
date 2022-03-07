<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Services\MessageTemplateService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Services\CompanyService;
use App\Traits\ResponseAPI;
use Illuminate\Support\Str;
use Exception;

class MessageTemplateController extends Controller
{
    use ResponseAPI;

    public $success_status = 200;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user= Auth::user();
            return $next($request);
        });
        $this->message_template_service = new MessageTemplateService;
        $this->company_service = new CompanyService;
    }
    public function getMessageTemplates(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company->id))
        {
            return $this->error("Company Not Found",404);
        }
        $message_templates =  $this->message_template_service->getData($company->id);
        $data=[];
        if(count($message_templates) > 0) {
            foreach($message_templates as $index=>$message_template)
            {
                $data[$index] = [
                    "uu_id" => $message_template->uu_id,
                    "nick_name" => $message_template->nick_name,
                    "template_text" => $message_template->template_text,
                    "company_id" => $company->uu_id,
                    "user_id" => Auth::user()->uu_id,
                    "created_at" => $message_template->created_at,
                    "updated_at" => $message_template->updated_at,
                ];
            }
            return $this->success($data);
        }
        else{
            return $this->error("Message Template Not Found",'404');
        }
    }

    public function create(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company->id))
        {
            return $this->error("Company Not Found",404);
        }
        $validator = Validator::make($request->all(), [
            'nick_name' => 'required',
            'template_text'=>'required',
            'companyId'=>'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = [];
        $data = [
            'nick_name' => $request->nick_name,
            'template_text'=>$request->template_text,
            'company_id'=>$company->id,
            'uu_id' => (string)Str::uuid(),
            'user_id' => $this->user->id,
         ];
        //  dd($data);
         try
         {
            $success = $this->message_template_service->store($data);
            $success['company_id'] = $company->uu_id;
            $success['user_id'] = Auth::user()->uu_id;
            return $this->success($success);
         }
         catch(Exception $e)
         {
            return $this->error($e->getMessage(),'404');
         }
    }

    public function show($id,Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company->id))
        {
            return $this->error("Company Not Found",404);
        }
        $data = $this->message_template_service->show($id,$company->id);
        if($data['status'] == true) {
            $data['Message'][0]['company_id'] = $company->uu_id;
            $data['Message'][0]['user_id'] = Auth::user()->uu_id;
            return $this->success($data);
        }
        else{
            return $this->error("Message Template Not Found",'404');
        }
    }

    public function update($id, Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company->id))
        {
            return $this->error("Company Not Found",404);
        }
        $validator = Validator::make($request->all(), [
            'template_text' => 'required',
            'companyId' => 'required',
            'nick_name' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }
        $template_text = $request->template_text;
        $nick_name = $request->nick_name;
        $data = [];
        $data = [
            'template_text' => $template_text,
            'company_id' => $company->id,
            'nick_name' => $nick_name,
        ];
        $success['update'] = $this->message_template_service->update($data,$id);
        if($success['update']['status'] == true) {
            return $this->success($success);
        }
        else{
            return $this->error("Message Template Not Found",'404');
        }

    }

    public function destroy($id,Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if(!isset($company->id))
        {
            return $this->error("Company Not Found",404);
        }
        $success['delete'] =  $this->message_template_service->destroy($id);
        if($success['delete']['status'] == true) {
            return $this->success($success);
        }
        else{
            return $this->error("Message Template Not Found",'404');
        }
    }
}
