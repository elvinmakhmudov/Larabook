<?php

use Larabook\Users\UserRepository;

class UsersController extends \BaseController {

    /**
     * @var
     */
    public $userRepository;

    /**
     * @param UserRepository $userRepository
     */
    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function users()
	{
        $users = $this->userRepository->getPaginated();

		return View::make('users.users')->withUsers($users);
	}

    public function profile($username)
    {
        $user = $this->userRepository->findByUsernameWithStatuses($username);

        return View::make('users.profile')->withUser($user);
    }
}
