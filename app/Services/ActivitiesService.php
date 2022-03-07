<?php

namespace App\Services;

use App\Repositories\ActivitiesRepository;
use Illuminate\Support\Facades\Auth;

class ActivitiesService
{
    protected $activity_repository;

    public function __construct()
    {
        $this->activity_repository = new ActivitiesRepository;
    }

    public function getLoginActivities()
    {
        return $this->activity_repository->getLoginActivities();
    }

    public function getLoginActivitiesByUser($user_id)
    {
        return $this->activity_repository->getLoginActivitiesByUser($user_id);
    }
    
    public function getAdminLoginActivities()
    {
        return $this->activity_repository->getAdminLoginActivities();
    }

    public function getCompanyActivities()
    {
        return $this->activity_repository->getCompanyActivities();
    }

    public function getCompanyActivitiesByUser($id)
    {
        return $this->activity_repository->getCompanyActivitiesByUser($id);
    }

}
