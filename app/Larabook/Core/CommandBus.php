<?php


namespace Larabook\Core;


use Illuminate\Support\Facades\App;


trait CommandBus {

    /**
     * Execute the command
     *
     * @param $command
     */
    public function execute($command)
    {
        return $this->getCommandBus()->execute($command);
    }

    /**
     * Fetch the command bus
     *
     * @return mixed
     */
    public function getCommandBus()
    {
        return App::make('Laracasts\Commander\CommandBus');
    }

} 