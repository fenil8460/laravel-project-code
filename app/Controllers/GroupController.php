<?php

namespace App\Controllers;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;
use App\Services\GroupService;
use App\Services\RegisterService;
use App\Services\CompanyService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseAPI;
use App\Events\CompanyActivity;
use Exception;

class GroupController extends Controller
{
    use ResponseAPI;

    public $success_status = 200;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
        $this->group_service = new GroupService;
        $this->company_service = new CompanyService;
        $this->register_service = new RegisterService;
    }
    public function getGroups()
    {
            $groups =  $this->group_service->getData();
            $data = [];
            if (count($groups) > 0) {
                foreach($groups as $index=>$group){
                    $user = $this->register_service->getUserByAdmin($group->user_id);
                    $company = $this->company_service->findByAdmin($group->company_id);
                    $data[$index]=[
                        "uu_id" => $group->uu_id,
                        "group_name" => $group->group_name,
                        "user_id" => isset($user->uu_id) ? $user->uu_id : null,
                        "company_id" => isset($company->uu_id) ? $company->uu_id : null,
                        "created_at" => $group->created_at,
                        "updated_at" => $group->updated_at,
                    ];
                }
                return $this->success($data);
            } else {
                return $this->error("Group Not Found", '404');
            }
    }

    public function createGroup(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }
        $validator = Validator::make($request->all(), [
            'group_name' => 'required',
            'companyId' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $data = [];
        $data = [
            'group_name' => $request->group_name,
            'company_id' => $company->id,
            'user_id' => Auth::user()->id,
        ];
        try {
            $success = $this->group_service->store($data);
            $company_activities = $success;
            $company_activities['type'] = 'groups';
            $company_activities['company_id'] = $company->uu_id;
            $company_activities['user_id'] = Auth::user()->uu_id;
            event(new CompanyActivity($company_activities));
            return $this->success($success);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), '404');
        }
    }

    public function getCompanyGroup(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }
        $groups = $this->group_service->getCompanyGroup($company->id);
        $data = [];
        if ($groups != null && count($groups) > 0) {
            foreach($groups as $index=>$group){
                $data[$index] = [
                    "uu_id" => $group->uu_id,
                    "group_name" => $group->group_name,
                    "user_id" => Auth::user()->uu_id,
                    "company_id" => $company->uu_id,
                    "created_at" => $group->created_at,
                    "updated_at" => $group->updated_at,
                ];
            }
            return $this->success($data);
        } else {
            return $this->error("Group Not Found", '404');
        }
    }

    public function getUserGroup()
    {
        $groups = $this->group_service->getUserGroup();
        if (count($groups) > 0) {
            foreach($groups as $index=>$group){
                $company = $this->company_service->find($group->company_id);
                $data[$index] = [
                    "uu_id" => $group->uu_id,
                    "group_name" => $group->group_name,
                    "user_id" => Auth::user()->uu_id,
                    "company_id" => isset($company->uu_id) ? $company->uu_id : null,
                    "created_at" => $group->created_at,
                    "updated_at" => $group->updated_at,
                ];
            }
            return $this->success($data);
        } else {
            return $this->error("Group Not Found", '404');
        }
    }
}
