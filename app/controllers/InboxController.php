<?php

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Larabook\Conversations\ConversationRepository;
use Larabook\Conversations\getConversationCommand;
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
        $input = [
            'sendToUsername' => Input::get('u')
        ];

        $conversation = $this->execute(getConversationCommand::class, $input);

        //get the all convs previews
        $previews= $this->conversationRepository->getPreviews();

        return View::make('inbox.show')->withPreviews($previews)
                                       ->withConversation($conversation);
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
