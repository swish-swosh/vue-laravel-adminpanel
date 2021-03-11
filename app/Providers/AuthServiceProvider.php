<?php

namespace App\Providers;

use App\Models\Organisation;
use App\Models\Resource;
use App\Models\ResourceType;
use App\Models\ResourceTypeGroup;
use App\Models\Role;
use App\Models\Log;

use App\Policies\OrganisationPolicy;
use App\Policies\ResourcePolicy;
use App\Policies\ResourceAccessPolicy;
use App\Policies\ResourceTypePolicy;
use App\Policies\ResourceTypeGroupPolicy;

use App\Policies\RolePolicy;
use App\Policies\LogPolicy;

use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Log::class => LogPolicy::class,
        Organisation::class => OrganisationPolicy::class,
        Resource::class => ResourcePolicy::class,
        ResourceAccess::class => ResourceAccessPolicy::class,
        ResourceType::class => ResourceTypePolicy::class,
        ResourceTypeGroup::class => ResourceTypeGroupPolicy::class,
        Role::class => RolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
        Passport::tokensExpireIn(now()->addMinutes(1140));
        Passport::refreshTokensExpireIn(now()->addMinutes(2280));
    
        // Passport::enableImplicitGrant();
        // Passport::personalAccessTokensExpireIn(now()->addMinutes(10));        
    }
}
