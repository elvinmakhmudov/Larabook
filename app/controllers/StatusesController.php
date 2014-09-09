<?php

use Larabook\Forms\PublishStatusForm;
use Larabook\Statuses\PublishStatusCommand;
use Larabook\Statuses\StatusRepository;

class StatusesController extends \BaseController {

    protected $statusRepository;
    protected  $publishStatusForm;

    function __construct(PublishStatusForm $publishStatusForm, StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
        $this->publishStatusForm = $publishStatusForm;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function show()
	{
        $statuses = $this->statusRepository->getAllForUser(Auth::user());
        return View::make('statuses.show', compact('statuses'));
	}

	/**
     * Save a new status
	 *
	 * @return Response
	 */
	public function publish()
	{
        $input = Input::get();
        $input['userId'] = Auth::id();

        $this->publishStatusForm->validate($input);

        $this->execute(PublishStatusCommand::class, $input);

        Flash::message('Your status has been updated');
        return Redirect::back();
	}
}
