<?php

use Larabook\Conversations\ConversationRepository;
use Larabook\Conversations\DeleteConversationCommand;
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
        $this->beforeFilter('auth');
    }

    /**
     * Show a new send message form
     *
     * @return mixed
     */
    public function index()
    {
        return View::make('inbox.new-message');
    }
    /**
     * Show dialog with user
     *
     * @return array
     */
    public function show()
    {
        $input = ['sendToUsername' => Input::get('u')];

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

        $this->sendMessageForm->validate($input);

        $this->execute(SendMessageCommand::class, $input);

        return Redirect::route('inbox_path');
    }

    /**
     * Delete a conversation
     */
    public function delete()
    {
        $input = ['otherUsername' => Input::get('otherUsername')];

        $this->execute(DeleteConversationCommand::class, $input);

        return Redirect::route('inbox_path');
    }
}
