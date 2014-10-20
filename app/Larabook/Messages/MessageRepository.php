<?php  namespace Larabook\Messages; 

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Conversation;
use Larabook\Users\CurrentUser;
use Larabook\Users\User;

class MessageRepository {

    public $currentUser;

    public function __construct(User $user = null)
    {
        $this->currentUser = $user ? Auth::user() : $user;
    }

    /**
     * Save a message in a conversation
     *
     * @param Message $message
     * @param Conversation $conversation
     */
    public function save(Message $message, Conversation $conversation)
    {
        $message->sender()->associate($this->currentUser);
        $message->conversation()->associate($conversation);

        $message->save();
    }
}