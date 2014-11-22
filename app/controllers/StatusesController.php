<?php

use Illuminate\Support\Facades\Response;
use Larabook\Forms\PublishStatusForm;
use Larabook\Statuses\PublishStatusCommand;
use Larabook\Statuses\StatusRepository;

class StatusesController extends \BaseController {

    public $ajaxActions = [
        'show',
        'publish'
    ];

    public $ajaxResponseFormat = [
        'id',
        'body',
        'created_at',
        'user' => [
            'username'
            ]
        ];

    protected $statusRepository;
    protected  $publishStatusForm;

    function __construct(PublishStatusForm $publishStatusForm, StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
        $this->publishStatusForm = $publishStatusForm;
        $this->beforeFilter('auth');
    }

    /**
     * Get the action name and if exists in allowed methods trigger the method
     *
     * @return \Illuminate\Http\Response
     */
    public function action()
    {
        return $this->doAction(Input::get('action'), $this->ajaxActions);
    }

    /**
     * Show the main page
     *
     * @return mixed
     */
    public function index()
    {
        return View::make('statuses.show');
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function show()
	{
        $statuses = $this->statusRepository->getFeed()->toArray();

        $statuses = $this->getAjaxResponseFor($statuses, $this->ajaxResponseFormat);

        return Response::json(compact('statuses'));
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

        return Response::json('okay', 200);

//        Flash::message('Your status has been updated');
//        return Redirect::back();
	}
}
