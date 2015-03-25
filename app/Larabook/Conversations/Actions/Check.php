<?php  namespace Larabook\Conversations\Actions; 

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Conversation;
use Larabook\Conversations\Exceptions\ConversationIsHiddenException;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\User;

class Check {

    public $currentUser;
    /**
     * @var Get
     */
    public $get;

    public function __construct($user = null, Get $get = null)
    {
        $this->currentUser = $user ? $user : Auth::user();
//        $this->get = $get ? new Get($this->currentUser) : $get;
        $this->get = $get ? $get : new Get($this->currentUser);
    }

    /**
     * Check whether conversation exists, if not throw an Exception
     *
     * @param $id
     * @return bool
     * @throws ConversationNotFoundException
     */
    public function existsOrFail($id)
    {
        if( ! $this->exists($id) )
        {
            throw new ConversationNotFoundException('Conversation not found');
        }

        return true;
    }

    /**
     * Check whether conversation is shown for the current user, if not throw an Exception
     *
     * @param $conversation
     * @throws ConversationIsHiddenException
     * @return bool
     */
    public function shownOrFail($conversation)
    {
        if( ! $this->isShown($conversation))
        {
            //send the hidden conversation as an argument
            throw new ConversationIsHiddenException('Conversation is hidden', $conversation);
        }

        return true;
    }

    /**
     * Is the conversation shown for the current user
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function isShown(Conversation $conversation)
    {
        return $this->isShownFor($this->currentUser, $conversation);
    }

    /**
     * Is the conversation shown for the user
     *
     * @param User $user
     * @param Conversation $conversation
     * @return bool
     */
    public function isShownFor(User $user, Conversation $conversation)
    {
        return ! $this->get->hiddenConvs($user)->find($conversation->id);
    }

    /**
     * Does the conversation one of the current User's conversations?
     *
     * @param $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->existsFor($this->currentUser, $id);
    }

    /**
     * Does the conversation one of the User's conversations?
     *
     * @param User $user
     * @param $id
     * @throws ConversationNotFoundException
     * @return bool
     */
    public function existsFor(User $user, $id)
    {
        $conversations = $this->get->getUserConvs($user);

        $convIds = $conversations->map(function($conversation)
        {
            return $conversation->id;
        })->toArray();

        if( ! in_array($id, $convIds))
        {
            return false;
        }

        return true;
    }

    /**
     * Can somebody see the conversation?
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function seenBySomebody(Conversation $conversation)
    {
        foreach($conversation->users as $user)
        {
            //is the conversation shown for the user
            if( $this->isShownFor($user, $conversation) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Is the given conversation unread by the current user
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function isUnread(Conversation $conversation)
    {
        return $this->currentUser->conversations()->find($conversation->id)->pivot->unread;
        return $this->get->unreadConvs()->find($conversation->id);
    }
}