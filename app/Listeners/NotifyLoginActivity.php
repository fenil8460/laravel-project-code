<?php

namespace App\Listeners;

use App\Events\LoginActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\LoginActivities;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\AdminLoginActivities;

class NotifyLoginActivity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\LoginActivity  $event
     * @return void
     */
    public function handle(LoginActivity $event)
    {
        if($event->activity == 'login')
        {
            $login_activities=[
                'user_type'=> User::class,
                'user_id'=> Auth::user()->id,
                'login_time'=> Carbon::now(),
                'ip_address'=> request()->ip(),
            ];
            LoginActivities::create($login_activities);
        }
        else if($event->activity == 'logout'){
            $activities = LoginActivities::where('user_id',Auth::user()->id)->orderBy('login_time', 'desc')->first();
            $update_activities = LoginActivities::where('uu_id',$activities->uu_id)->update([
                'logout_time'=>Carbon::now()
            ]);
        }
        else if(isset($event->activity['event']) && $event->activity['event'] == 'admin-login'){
            $login_activities=[
                'user_type'=> Admin::class,
                'admin_id'=> $event->activity['admin_id'],
                'login_time'=> Carbon::now(),
                'ip_address'=> request()->ip(),
            ];
            AdminLoginActivities::create($login_activities);
        }
        else if($event->activity == 'admin-logout'){
            $user = Auth::guard('admin')->user();
            $activities = AdminLoginActivities::where('admin_id',$user->id)->orderBy('login_time', 'desc')->first();
            $update_activities = AdminLoginActivities::where('uu_id',$activities->uu_id)->update([
            'logout_time'=>Carbon::now()
        ]);
        }
        else if(isset($event->activity['event']) && $event->activity['event'] == 'admin-user-login')
        {
            $login_activities=[
                'user_type'=> Admin::class,
                'admin_id'=> $event->activity['admin_id'],
                'user_id'=>$event->activity['user_id'],
                'login_time'=> Carbon::now(),
                'ip_address'=> request()->ip(),
            ];
            AdminLoginActivities::create($login_activities);
        }
    }
}
