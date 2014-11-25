<?php namespace Larabook\Statuses\Ajax;

class GetStatusesCommand {
    
    public $page;

    /**
     */
    public function __construct($page)
    {
        $this->page = $page;
    }

}