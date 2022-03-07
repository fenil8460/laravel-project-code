<?php

namespace App\Repositories;

use App\Models\Notification;

class NotificationRepository
{
    public function viewLatestNotifications($company_id)
    {
        return Notification::where('notifiable_type',"App\Models\Company")->where('notifiable_id',$company_id)->orderBy('created_at','DESC')->latest()->take(5)->get();
    }

    public function viewAllNotifications($company_id)
    {
        return Notification::where('notifiable_type',"App\Models\Company")->where('notifiable_id',$company_id)->get();
    }
}
