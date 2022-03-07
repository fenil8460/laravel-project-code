<?php

namespace App\Repositories;

use App\Traits\FindAPI;
use Rinvex\Subscriptions\Models\PlanFeature;

class PlanFeatureRepository
{

    use FindAPI;

    public function createFeature($data)
    {
        $feature = Role::create($data);
        return $feature;
    }

    public function findFeature($id)
    {
        return PlanFeature::where('id',$id)->first();
    }

    public function getFeatures()
    {
        return PlanFeature::all();
    }

    public function updateFeature($data,$id)
    {
        $feature = PlanFeature::where('id',$id)->first();
         return $this->updateResource($feature,$data);
    }

    public function deleteFeature($id)
    {
        $feature = PlanFeature::where('id',$id);
        return $this->destroyResource($feature);
    }

    public function deleteFeatureByPlan($id)
    {
        $feature = PlanFeature::where('plan_id',$id);
        return $this->destroyResource($feature);
    }

}
