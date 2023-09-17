<?php

namespace App\Providers;

use App\Repositories\Contracts\UserContract;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class UserSerivceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserContract::class,UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
