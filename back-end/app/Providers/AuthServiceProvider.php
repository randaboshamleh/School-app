<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        \App\Models\Payment::class => \App\Policies\PaymentPolicy::class,
        \App\Models\ExamScore::class => \App\Policies\ExamScorePolicy::class,
        \Illuminate\Notifications\DatabaseNotification::class => \App\Policies\NotificationPolicy::class,

    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {

        $this->registerPolicies();

        Gate::define('send-notifications', function ($user) {
            return $user->is_admin;
        });

    }
}
