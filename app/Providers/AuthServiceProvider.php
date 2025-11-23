<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        \App\Models\User::class => \App\Policies\UserPolicy::class,
        \App\Models\Publicacion::class => \App\Policies\PublicacionPolicy::class,
    ];


    public function boot()
    {
    $this->registerPolicies();
    }

}
