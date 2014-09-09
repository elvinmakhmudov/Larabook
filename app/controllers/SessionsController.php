<?php

use Larabook\Forms\SignInForm;

class SessionsController extends \BaseController {

    /**
     * @var Larabook\Forms\SignInForm
     */
    private $signInForm;

    public function __construct(SignInForm $signInForm)
    {
        $this->signInForm = $signInForm;
        $this->beforeFilter('guest', ['except' => 'destroy']);
    }
	/**
	 * Show the form for Signing in.
	 *
	 * @return Response
	 */
	public function show()
	{
        return View::make('sessions.show');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function login()
	{
        $formData = Input::only('email', 'password');

        $this->signInForm->validate($formData);

        if( ! Auth::attempt($formData))
        {
             Flash::message('We were unable to sign you in. Please check your credentials.');

            return Redirect::back()->withInput();
        }

        Flash::message('Welcome back!');

        return Redirect::intended('/statuses');
	}

    /**
     * Log a user out of Larabook
     * @return mixed
     */
    public function logout()
    {
        Auth::logout();

        return Redirect::home();
    }

}
