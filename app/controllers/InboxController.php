<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Larabook\Conversations\ConversationRepository;
use Larabook\Messages\SendMessageCommand;
use Larabook\Forms\SendMessageForm;
use Larabook\Users\UserRepository;

class InboxController extends \BaseController {

    public $sendMessageForm;

    public $userRepository;

    private $conversationRepository;

    function __construct(SendMessageForm $sendMessageForm, UserRepository $userRepository, ConversationRepository $conversationRepository)
    {
        $this->sendMessageForm = $sendMessageForm;
        $this->userRepository = $userRepository;
        $this->conversationRepository = $conversationRepository;
    }

    /**
     * Show dialog with user
     *
     * @return array
     */
    public function show()
    {
        //TODO::find out is it necessary to validate the get data
        $username = Input::get('u');
        $user = Auth::user();

        //TODO::if possible minimize the code somehow
        try {
            //get the user by username
            $otherUser = $this->userRepository->findbyUsername($username);

            //get the conversation between users
            $mainConv = $this->conversationRepository->getConversationWith($otherUser);

        } catch(ModelNotFoundException $e) {

            $mainConv = $this->conversationRepository->getLastConversationFor($user);
        }

        //get the all convs previews
        $previews= $this->conversationRepository->getPreviews($user);

        return View::make('inbox.show')->withPreviews($previews)
                                       ->with('mainConv', $mainConv);
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
}
