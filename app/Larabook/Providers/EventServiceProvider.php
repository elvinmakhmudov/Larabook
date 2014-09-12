<?php  namespace Larabook\Providers; 

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider{


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app['events']->listen('Larabook.*', 'Larabook\Handlers\EmailNotifier');
    }
}