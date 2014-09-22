<?php

use Illuminate\Support\Facades\Validator;
use Larabook\Inbox\Conversation;
use Larabook\Inbox\InboxRepository;
use Larabook\Inbox\SendMessageCommand;
use Larabook\Forms\SendMessageForm;
use Larabook\Users\UserRepository;

class InboxController extends \BaseController {

    public $sendMessageForm;

    public $userRepository;
    /**
     * @var InboxRepository
     */
    private $inboxRepository;

    /**
     * @param SendMessageForm $sendMessageForm
     * @param UserRepository $userRepository
     * @param InboxRepository $inboxRepository
     */
    function __construct(SendMessageForm $sendMessageForm, UserRepository $userRepository, InboxRepository $inboxRepository)
    {
        $this->sendMessageForm = $sendMessageForm;
        $this->userRepository = $userRepository;
        $this->inboxRepository = $inboxRepository;
    }

    /**
     * Display the messages.
     *
     * @internal param int $id
     * @return Response
     */
	public function show()
	{
        // get the username of the last conversation
//        $convs = Auth::user()->conversations->last()->messages->last()->sender->username;
        $username = $this->inboxRepository->getOtherUserInConversation(Auth::user()->conversations->last(), Auth::user());

        dd($username);

        dd($convs);

        return View::make('inbox.show')->withConvs($convs);
	}

    /**
     * Send a message
     */
    public function send()
    {
        $input = Input::all();
        $input['userId'] = Auth::id();

        $this->sendMessageForm->validate($input);

        $this->execute(SendMessageCommand::class, $input);

        return Redirect::back();
    }

    /**
     * Show dialog with user
     *
     * @param $username
     * @return array
     */
    public function showDialog($username)
    {
        $validator = Validator::make(['username' => $username],
                                     ['username' => 'required']);

        if($validator->fails()) return Redirect::home();

        $otherUser = $this->userRepository->findbyUsername($username);

        $convId = $this->inboxRepository->getConversationId(Auth::user(), $otherUser);

        $conv = Conversation::find($convId)->with('messages', 'users');

        return View::make('inbox.showDialog')->withConv($conv);
    }
}
