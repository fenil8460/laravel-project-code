<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\LoginActivity;
use App\Listeners\NotifyLoginActivity;
use App\Events\CompanyActivity;
use App\Listeners\NotifyCompanyActivity;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LoginActivity::class => [
            NotifyLoginActivity::class,
        ],
        CompanyActivity::class => [
            NotifyCompanyActivity::class,
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Microsoft\MicrosoftExtendSocialite::class.'@handle',
            \SocialiteProviders\Azure\AzureExtendSocialite::class.'@handle',
            \SocialiteProviders\Graph\GraphExtendSocialite::class.'@handle',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
