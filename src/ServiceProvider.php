<?php namespace Visualplus\Helper;

class ServiceProvider extends \Illuminate\Support\ServiceProvider {
    /**
     * @return void
     */
    public function boot()
    {

    }

    /**
     * @return void
     */
    public function register()
    {
        $routingServiceProvider = new RoutingServiceProvider($this->app);
        $routingServiceProvider->register();
    }
}