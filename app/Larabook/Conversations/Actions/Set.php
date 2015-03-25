<?php  namespace Larabook\Conversations\Actions; 

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Conversation;
use Larabook\Users\User;

class Set {

    public $currentUser;

    public function __construct($user = null)
    {
        $this->currentUser = $user ? $user : Auth::user();
    }

    /**
     * Set hidden field to true for the conversation
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function hiddenFor(Conversation $conversation)
    {
        $this->currentUser->conversations()->updateExistingPivot($conversation->id, ['hidden' => true,
            'hidden_date' => Carbon::now()]);

        return true;
    }

    /**
     * Set hidden field of the conversation to false for the current user
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function shown(Conversation $conversation)
    {
        return $this->shownFor($this->currentUser, $conversation);
    }

    /**
     * Set hidden field of the conversation to false for the user
     *
     * @param User $user
     * @param Conversation $conversation
     * @return \Larabook\Conversations\Conversationn
     */
    public function shownFor(User $user, Conversation $conversation)
    {
        $user->conversations()->updateExistingPivot($conversation->id, ['hidden' => false]);

        return true;
    }

    /**
     * Set the unread column to true
     *
     * @param User $user
     * @param Conversation $conversation
     * @return bool
     */
    public function unread(User $user, Conversation $conversation)
    {
        $user->conversations()->updateExistingPivot($conversation->id, ['unread' => true]);

        return true;
    }

    /**
     * Set the conversation to read
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function read(Conversation $conversation)
    {
        $this->currentUser->conversations()->updateExistingPivot($conversation->id, ['unread' => false ]);

        //update the local pivot records for performance improvements
        $this->currentUser->conversations->find($conversation->id)->pivot->unread = false;

        return true;
    }
}