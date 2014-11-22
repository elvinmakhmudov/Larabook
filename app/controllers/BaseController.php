<?php

use controllers\Traits\ActionTrait;
use controllers\Traits\AjaxResponseTrait;
use Laracasts\Commander\CommanderTrait;

class BaseController extends Controller {

    use CommanderTrait, ActionTrait, AjaxResponseTrait;
	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}

        View::share('currentUser', Auth::user());
        View::share('signedIn', Auth::user());
	}

}
