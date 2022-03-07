<?php

namespace App\Services;

use App\Repositories\NotificationRepository;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    protected $notification_repository;
    public function __construct()
    {

        $this->notification_repository = new NotificationRepository;

    }

    public function viewNotifications($company_id)
    {
        $latest_notifications= $this->notification_repository->viewLatestNotifications($company_id);
        $all_notification = $this->notification_repository->viewAllNotifications($company_id);
        return["latest" => $latest_notifications, "all" => $all_notification];
    }
}
