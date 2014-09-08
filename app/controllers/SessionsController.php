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
	public function create()
	{
        return View::make('sessions.create');
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $formData = Input::only('email', 'password');
        $this->signInForm->validate($formData);
        if(Auth::attempt($formData))
        {
            Flash::message('Welcome back!');
            return Redirect::intended('/statuses');
        }
	}

    /**
     * Log a user out of Larabook
     * @return mixed
     */
    public function destroy()
    {
        Auth::logout();

        return Redirect::home();
    }

}