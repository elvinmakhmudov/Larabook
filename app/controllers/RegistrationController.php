<?php

use Illuminate\Support\Facades\Auth;
use Larabook\Forms\RegistrationForm;
use Larabook\Registration\RegisterUserCommand;

class RegistrationController extends \BaseController {

    /**
     * @var RegistrationForm
     */
    private $registrationForm;

    function __construct(RegistrationForm $registrationForm)
    {

        $this->registrationForm = $registrationForm;
        $this->beforeFilter("guest");
    }

    /**
     * Show a form to register a user
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('registration.create');
	}

    public function store()
    {

        $this->registrationForm->validate(Input::all());

        $user = $this->execute(RegisterUserCommand::class);

        Auth::login($user);

        Flash::success('Glad to have you as a Larabook member');

        return Redirect::home();
    }

}
