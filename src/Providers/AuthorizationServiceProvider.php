<?php

namespace Betterde\Authorization\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Date: 19/04/2018
 * @author George
 * @package Betterde\Role\Providers
 */
class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * 发布数据库迁移文件
         */
        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations'),
            __DIR__.'/../../config/authorization.php' => config_path('authorization.php')
        ], 'authorization');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
