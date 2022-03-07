<?php

namespace App\Listeners;

use App\Events\CompanyActivity;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\User;
use App\Models\Company;
use App\Models\CompanyActivities;
use Illuminate\Support\Str;
use App\Services\PhoneNumberService;
use App\Models\PhoneNumber;
use App\Models\Contact;
use App\Models\Group;
use App\Models\GroupContact;
use Bavix\Wallet\Models\Wallet;
use App\Models\SmsMessageOut;
use App\Models\SmsMessageIn;

class NotifyCompanyActivity
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->phone_number_service = new PhoneNumberService;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\CompanyActivity  $event
     * @return void
     */
    public function handle(CompanyActivity $event)
    {
        //
        $type='';
        $company_id = null;
        $activity = null;
        $phone_id = null;
        $buy_number = null;
        $disocnnect = null;
        $reconnect = null;
        $message = null;
        $group = null;
        $contacts = null;
        $group_contacts = null;
        $wallet = null;
        if(isset($event->activity['type']) && $event->activity['type'] =='company')
        {
            $type = Company::class;
            $company_id = $event->activity['id'];
            $activity = 'company created';
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='buy_number')
        {
            $data = $this->phone_number_service->findNumber($event->activity['phone_number']);
            $buy_number = $event->activity['phone_number'];
            $phone_id = $data->id;
            $type = PhoneNumber::class;
            $company_id = $event->activity['company_id'];
            if($event->activity['order_status'] == 'complete')
            {
                $activity = "phone number purchased";
            }else if($event->activity['order_status'] == 'partial')
            {
                $activity = "phone number purchased partially";
            }else
            {
                $activity = "phone number purchase failed";
            }
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='disconnected')
        {
            $data = $this->phone_number_service->findNumber($event->activity['phone_number']);
            $disocnnect = $event->activity['phone_number'];
            $phone_id = $data->id;
            $type = PhoneNumber::class;
            $company_id = $event->activity['company_id'];
            if($event->activity['order_status'] == 'disconnected')
            {
                $activity = "numbers are disconnected";
            }else if($event->activity['order_status'] == 'already disconnected')
            {
                $activity = "numbers are already disconnected";
            }
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='reconnect')
        {
            $reconnect = $event->activity['phone_number'];
            $phone_id = $event->activity['id'];
            $type = PhoneNumber::class;
            $company_id = $event->activity['company_id'];
            if($event->activity['order_status'] == 'success')
            {
                $activity = "numbers are reconnected";
            }
            else
            {
                $activity = "numbers are already reconnected";
            }
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='wallet')
        {
            $reconnect = $event->activity['phone_number'];
            $type = Wallet::class;
            $company_id = $event->activity['id'];
            $wallet = $event->activity['amount'];
            if($event->activity['status'] == 'deposit'){
                $activity = 'amount deposit';
            }else{
                $activity = 'amount withdraw';
            }
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='contacts')
        {
            $type = Contact::class;
            $company_id = $event->activity['company_id'];
            $activity = 'contact created';
            $contacts = $event->activity['id'];
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='groups')
        {
            $type = Group::class;
            $company_id = $event->activity['company_id'];
            $activity = 'group created';
            $group = $event->activity['id'];
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='groups contacts')
        {
            $type = GroupContact::class;
            $contacts = $event->activity['contact_id'];
            $company_id = $event->activity['company_id'];
            $activity = 'add contact in group';
            $group_contacts = $event->activity['id'];
            $group = $event->activity['group_id'];
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='message')
        {
            $type = SmsMessageOut::class;
            $company_id = $event->activity['company_id'];
            $phone_id = $event->activity['phone_number_id'];
            $activity = 'send a message';
            $message = $event->activity['id'];
        }
        else if(isset($event->activity['type']) && $event->activity['type'] =='messageReceive')
        {
            $type = SmsMessageIn::class;
            $company_id = $event->activity['company_id'];
            $phone_id = $event->activity['phone_number_id'];
            $activity = 'receive a message';
            $message = $event->activity['id'];
        }
            $data = [
                'type' => $type,
                'uu_id' => (string)Str::uuid(),
                'company_id' => $company_id,
                'activity' => $activity,
                'phone_id' => $phone_id,
                'buy_number' => $buy_number,
                'disocnnect' => $disocnnect,
                'reconnect' => $reconnect,
                'ip_address' => request()->ip(),
                'message' => $message,
                'group' => $group,
                'contacts' => $contacts,
                'group_contacts' => $group_contacts,
                'wallet' => $wallet,
             ];
            
            $company_activity = CompanyActivities::create($data);
               
        
    }
}
