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
}