<?php

namespace App\Services;

use App\Repositories\PlanFeatureRepository;

class PlanFeatureService
{
    protected $plan_feature_repository;

    public function __construct()
    {
        $this->plan_feature_repository = new PlanFeatureRepository;
    }

    public function createFeature($data)
    {
        return $this->plan_feature_repository->createFeature($data);
    }

    public function findFeature($id)
    {
        return $this->plan_feature_repository->findFeature($id);
    }
    public function updateFeature($data,$id)
    {
        return $this->plan_feature_repository->updateFeature($data,$id);

    }
    public function deleteFeature($id)
    {
        return $this->plan_feature_repository->deleteFeature($id);
    }

    public function deleteFeatureByPlan($id)
    {
        return $this->plan_feature_repository->deleteFeatureByPlan($id);
    }

    public function getFeatures()
    {
        return $this->plan_feature_repository->getFeatures();
    }
   
}
