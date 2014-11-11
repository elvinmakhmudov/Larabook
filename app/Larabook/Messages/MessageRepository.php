<?php  namespace Larabook\Messages; 

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Conversation;
use Larabook\Messages\Exceptions\MessageNotFoundException;
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


    /**
     * Get all messages for the conversation since hidden date
     *
     * @param Conversation $conversation
     * @param $hiddenDate
     */
    public function getAllFor(Conversation $conversation, $hiddenDate)
    {
        return $conversation->messages()->where('created_at', '>', $hiddenDate)->with('sender')->orderBy('created_at')->get()->reverse();
    }

    /**
     * Get the last message of the conversation
     *
     * @param Conversation $conversation
     * @throws MessageNotFoundException
     * @return
     */
    public function getLastMessage(Conversation $conversation)
    {
        //first() method because in the messages relationship we get the messages with latest() method
        $message = $conversation->messages()->with('sender')->first();

        if( ! is_null($message) ) return $message;

        throw new MessageNotFoundException;
    }
}