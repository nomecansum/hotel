<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;


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
        'Illuminate\Log\Events\MessageLogged' => [
            'App\Listeners\MessageLoggedListener',
        ],
        'Illuminate\Auth\Events\Login' => [
            'App\Listeners\SuccessfulLogin',
        ],
        'Slides\Saml2\Events\SignedIn' => [
            'App\Listeners\saml2SuccessfulLogin',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            // ... other providers
            \SocialiteProviders\Microsoft\MicrosoftExtendSocialite::class.'@handle',
        ],

    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }

}
