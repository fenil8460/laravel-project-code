<?php

namespace App\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CompanyService;
use App\Services\PlanFeatureService;
use App\Traits\ResponseAPI;
use BandwidthLib\Http\HttpContext;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Rinvex\Subscriptions\Models\PlanFeature;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    use ResponseAPI;
    protected $company_service;
    protected $plan_feature_service;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->user = Auth::user();
            return $next($request);
        });
        $this->company_service = new CompanyService();
        $this->plan_feature_service = new PlanFeatureService();
    }

    public function createPlan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'description' => 'string|max:32768',
            'price' => 'required|numeric',
            'signupFee' => 'required|numeric',
            'trialPeriod' => 'sometimes|integer|max:100000',
            'trialInterval' => 'sometimes|in:hour,day,week,month',
            'invoicePeriod' => 'sometimes|integer|max:100000',
            'invoiceInterval' => 'sometimes|in:hour,day,week,month',
            'currency' => 'required|alpha|size:3',

        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }
        try {
            $plan = app('rinvex.subscriptions.plan')->create([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'signup_fee' => $request->signupFee,
                'invoice_period' => $request->invoicePeriod ? $request->invoicePeriod : 0,
                'invoice_interval' => $request->invoiceInterval ? $request->invoiceInterval : "month",
                'trial_period' => $request->trialPeriod ? $request->trialPeriod : 0,
                'trial_interval' => $request->trialInterval ? $request->trialInterval : "day",
                'sort_order' => 1,
                'currency' => $request->currency,
            ]);
            if ($plan) {
                return $this->success($plan);
            } else {
                return $this->error('Something Went Wrong', 500);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function addPlanFeature(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'plan' => 'required',
            'value' => 'required',
            'resettablePeriod' => 'required',
            'resettableInterval' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }
        try {
            $plan = app('rinvex.subscriptions.plan')->find($request->plan);
            if ($plan) {
                $plan_feature = $plan->features()->save(
                    new PlanFeature([
                        'name' => $request->name,
                        'value' => $request->value,
                        'sort_order' => $request->sortOrder,
                        'resettable_period' => $request->resettablePeriod,
                        'resettable_interval' => $request->resettableInterval,
                    ]),
                );
                $data = [
                    'plan' => $plan,
                    'planFeature' => $plan_feature,
                ];
                return $this->success($data);
            } else {
                return $this->error('Something Went Wrong', 500);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function getAllplanFeature()
    {
        $plan_features = $this->plan_feature_service->getFeatures();
        $data = [];
        if(count($plan_features) > 0){
            foreach($plan_features as $index=>$feature){
                $data[$index] = [
                    "id"=> $feature->id,
                    "plan_id"=> $feature->plan_id,
                    "plan_name"=> app('rinvex.subscriptions.plan')->find($feature->plan_id)->name,
                    "slug"=> $feature->slug,
                    "name"=> $feature->name,
                    "description"=> $feature->description,
                    "value"=> $feature->value,
                    "resettable_period"=> $feature->resettable_period,
                    "resettable_interval"=> $feature->resettable_interval,
                    "sort_order"=> $feature->sort_order,
                    "created_at"=> $feature->created_at,
                    "updated_at"=> $feature->updated_at,
                    "deleted_at"=> $feature->deleted_at
                ];
            }
            return $this->success($data);
        }else{
            return $this->error('Plan Feature Not Found', 500);
        }
    }

    public function findplanFeature($id)
    {
        $plan_features = $this->plan_feature_service->findFeature($id);
        $data = [];
        if($plan_features != null){
                $data = [
                    "id"=> $plan_features->id,
                    "plan_id"=> $plan_features->plan_id,
                    "plan_name"=> app('rinvex.subscriptions.plan')->find($plan_features->plan_id)->name,
                    "slug"=> $plan_features->slug,
                    "name"=> $plan_features->name,
                    "description"=> $plan_features->description,
                    "value"=> $plan_features->value,
                    "resettable_period"=> $plan_features->resettable_period,
                    "resettable_interval"=> $plan_features->resettable_interval,
                    "sort_order"=> $plan_features->sort_order,
                    "created_at"=> $plan_features->created_at,
                    "updated_at"=> $plan_features->updated_at,
                    "deleted_at"=> $plan_features->deleted_at
                ];
            return $this->success($data);
        }else{
            return $this->error('Plan Feature Not Found', 500);
        }
    }

    public function destroyplanFeature($id)
    {
        $plan_features = $this->plan_feature_service->deleteFeature($id);
        if($plan_features['status'] == true){
            return $this->success($plan_features);
        }else{
            return $this->error('Plan Feature Not Found', 500);
        }
    }

    public function updateplanFeature($id,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'plan' => 'required',
            'value' => 'required',
            'resettablePeriod' => 'required',
            'resettableInterval' => 'required',
        ]);   
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        $data = [
            'name' => $request->name,
            'plan_id'=>$request->plan,
            'value' => $request->value,
            'sort_order' => $request->sortOrder,
            'resettable_period' => $request->resettablePeriod,
            'resettable_interval' => $request->resettableInterval,
        ];
        $plan_features = $this->plan_feature_service->updateFeature($data,$id);
        if($plan_features['status'] == true){
            return $this->success($plan_features);
        }else{
            return $this->error('Plan Feature Not Found', 500);
        }
    }

    public function getPlanFeatures(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }
        $plan = app('rinvex.subscriptions.plan')->find($request->plan);
        if ($plan) {
            $plan_features = $plan->features;

            if (isset($plan_features) && count($plan_features) > 0) {
                return $this->success($plan_features);
            }
            else
            {
                return $this->error('No Plan Features for this plan', 404);
            }
        } else {
            return $this->error('Requested Plan not found', 500);
        }
    }


    public function getAllPlans(Request $request)
    {

        $plans = app('rinvex.subscriptions.plan')->all();
        if ($plans->count() != 0) {
            $data = [];
            foreach ($plans as $plan) {
                $data[] =
                    [
                        "id" => $plan->id,
                        'uu_id' => $plan->uu_id,
                        "slug" => $plan->slug,
                        "name" => $plan->name,
                        "description" => $plan->description,
                        "isActive" => $plan->is_active,
                        "price" => $plan->price,
                        "signupFee" => $plan->signup_fee,
                        "currency" => $plan->currency,
                        "trialPeriod" => $plan->trial_period,
                        "trialInterval" => $plan->trial_interval,
                        "invoicePeriod" => $plan->invoice_period,
                        "invoiceInterval" => $plan->invoice_interval,
                        "planFeatures" => $plan->features,

                    ];
            }
            return $this->success($data);
        } else {
            return $this->error("No plans", 404);
        }
    }

    public function findPlan($id)
    {
        $plan = app('rinvex.subscriptions.plan')->find($id);
        if ($plan) {
            $data = [];
            $data = [
                "id" => $plan->id,
                "slug" => $plan->slug,
                "name" => $plan->name,
                "description" => $plan->description,
                "isActive" => $plan->is_active,
                "price" => $plan->price,
                "signupFee" => $plan->signup_fee,
                "currency" => $plan->currency,
                "trialPeriod" => $plan->trial_period,
                "trialInterval" => $plan->trial_interval,
                "invoicePeriod" => $plan->invoice_period,
                "invoiceInterval" => $plan->invoice_interval,
                "planFeatures" => $plan->features,
            ];
            return $this->success($data);
        } else {
            return $this->error("No plan", 404);
        }
    }

    public function destroyPlan($id)
    {
        if ($id) {
            $success['delete'] = app('rinvex.subscriptions.plan')->where('id',$id)->delete();
            $plan_features = $this->plan_feature_service->deleteFeatureByPlan($id);
            $success['delete'] = 'Deleted successfully';
            return $this->success($success);
        } else {
            return $this->error("No plan", 404);
        }
    }
    public function updatePlan($id,Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'description' => 'string|max:32768',
            'price' => 'required|numeric',
            'signupFee' => 'required|numeric',
            'trialPeriod' => 'sometimes|integer|max:100000',
            'trialInterval' => 'sometimes|in:hour,day,week,month',
            'invoicePeriod' => 'sometimes|integer|max:100000',
            'invoiceInterval' => 'sometimes|in:hour,day,week,month',
            'currency' => 'required|alpha|size:3',

        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }
        try {
            $plan = app('rinvex.subscriptions.plan')->find($id);
            $updateplan = $plan->update([
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'signup_fee' => $request->signupFee,
                'invoice_period' => $request->invoicePeriod ? $request->invoicePeriod : 0,
                'invoice_interval' => $request->invoiceInterval ? $request->invoiceInterval : "month",
                'trial_period' => $request->trialPeriod ? $request->trialPeriod : 0,
                'trial_interval' => $request->trialInterval ? $request->trialInterval : "day",
                'sort_order' => 1,
                'currency' => $request->currency,
            ]);
            if ($plan) {
                return $this->success($updateplan);
            } else {
                return $this->error('Something Went Wrong', 500);
            }
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function createSubscription(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }

        $validator = Validator::make($request->all(), [
            'companyId' => 'required',
            'plan' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }

        $plan = app('rinvex.subscriptions.plan')->find($request->plan);
        if (!$plan) {
            return $this->error("No Such Plan", 404);
        }
        try {
            $subscription = $company->newSubscription('Subscription for ' . $company->name, $plan);
            return $this->success($subscription);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function getCompanySubscriptions(Request $request)
    {
        $company = $this->company_service->findByUuid($request->companyId, $this->user->id);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }
        try {
            $active_subscriptions = $company->activeSubscriptions();
            if(count($active_subscriptions) == 0)
            {
                return $this->error('No Active Subscriptions',404);
            }
            return $this->success($active_subscriptions);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }


    }

    public function createSubscriptionByAdmin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyId' => 'required',
            'plan' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors());
        }
        $company = $this->company_service->adminFindByUuid($request->companyId);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }

        $plan = app('rinvex.subscriptions.plan')->find($request->plan);
        if (!$plan) {
            return $this->error("No Such Plan", 404);
        }
        try {
            $subscription = $company->newSubscription('Subscription for ' . $company->name, $plan);
            return $this->success($subscription);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }
    }

    public function getAllCompanySubscriptionsByAdmin(Request $request)
    {
        $companies = $this->company_service->getAllCompanies();
        $data = [];
        $a=0;
        foreach($companies as $company){
            if($company->activeSubscriptions()){
                $active_subscriptions = $company->activeSubscriptions();
                if(count($active_subscriptions) != 0)
                {
                    foreach($active_subscriptions as $active_subscription){
                        $data[$a]=$active_subscription;
                        $a++;
                    }
                }
                
            }
        }
        return $this->success($data);
    }

    public function getCompanySubscriptionsByAdmin(Request $request)
    {
        $company = $this->company_service->adminFindByUuid($request->companyId);
        if (!isset($company)) {
            return $this->error("Company Not Found", '404');
        }
        try {
            $active_subscriptions = $company->activeSubscriptions();
            if(count($active_subscriptions) == 0)
            {
                return $this->error('No Active Subscriptions',404);
            }
            return $this->success($active_subscriptions);
        } catch (Exception $e) {
            return $this->error($e->getMessage(), 500);
        }


    }
}
