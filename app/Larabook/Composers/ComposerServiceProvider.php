<?php namespace Larabook\Composers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider {


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->view->composer('layouts.partials.nav', 'Larabook\Composers\MessagesComposer');
    }
}