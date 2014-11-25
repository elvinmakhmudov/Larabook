<?php

use Illuminate\Support\Facades\Response;
use Larabook\Forms\PublishStatusForm;
use Larabook\Statuses\Ajax\GetStatusesCommand;
use Larabook\Statuses\Ajax\ShowStatusesRequest;
use Larabook\Statuses\PublishStatusCommand;

class StatusesController extends \BaseController {

    public $ajaxActions = [
        'show',
        'publish'
    ];

    //what values should response have
    public $ajaxResponseFormat = [
        'id',
        'body',
        'created_at',
        'user' => [
            'username'
        ]
    ];

    protected  $publishStatusForm;
    /**
     * @var ShowStatusesRequest
     */
    private $showStatusesRequest;

    function __construct(PublishStatusForm $publishStatusForm, ShowStatusesRequest $showStatusesRequest)
    {
        $this->publishStatusForm = $publishStatusForm;
        $this->showStatusesRequest = $showStatusesRequest;
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
        //validate the ajax request
        $this->showStatusesRequest->validate(Input::all());

        //get statuses
        $statuses = $this->execute(GetStatusesCommand::class);

        //get in given ajax response format
        $statuses = $this->getAjaxResponseFor($statuses, $this->ajaxResponseFormat);

        //response
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

        //publish the status
        $status = $this->execute(PublishStatusCommand::class, $input)->toArray();

        //add the user to the status array
        $status['user'] = Auth::user();

        $status = $this->getAjaxResponse($status, $this->ajaxResponseFormat);

        return Response::json($status);

//        Flash::message('Your status has been updated');
//        return Redirect::back();
	}
}
