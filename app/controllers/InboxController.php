<?php

use Larabook\Conversations\DeleteConversationCommand;
use Larabook\Conversations\getConversationCommand;
use Larabook\Conversations\Previews\getPreviewsCommand;
use Larabook\Messages\SendMessageCommand;
use Larabook\Forms\SendMessageForm;

class InboxController extends \BaseController {

    public $sendMessageForm;


    function __construct(SendMessageForm $sendMessageForm)
    {
        $this->sendMessageForm = $sendMessageForm;
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

        $previews = $this->execute(getPreviewsCommand::class);

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
        $input = ['convToDelete' => Input::get('convToDelete')];

        $this->execute(DeleteConversationCommand::class, $input);

        return Redirect::route('inbox_path');
    }
}
