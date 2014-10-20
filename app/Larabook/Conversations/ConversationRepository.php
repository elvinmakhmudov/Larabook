<?php  namespace Larabook\Conversations;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Exceptions\ConversationIsHiddenException;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\User;

class ConversationRepository {

    public $currentUser;

    public function __construct(User $user = null)
    {
        $this->currentUser = $user ? Auth::user() : $user;
    }

    /**
     * Get conversation between users
     *
     * @param User $otherUser
     * @throws ConversationIsHiddenException
     * @throws ConversationNotFoundException
     * @return array
     */
    public function getConversationWith(User $otherUser)
    {
        $conversation = $this->getConversationBetween($this->currentUser, $otherUser);

        //if the the conversation is shown for the current user
        if ($this->isShown($conversation))
        {
            return $conversation;
        }

        throw new ConversationIsHiddenException;
    }

    /**
     * Get conversation between 2 users
     *
     * @param User $user
     * @param User $otherUser
     * @return mixed
     * @throws ConversationNotFoundException
     */
    public function getConversationBetween(User $user, User $otherUser)
    {
        $convId = $this->getConversationIdBetween($user, $otherUser);

        return $this->findById($convId);
    }


    /**
     * Find conversation by Id
     *
     * @param $id
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function findById($id)
    {
        if( ! $this->isConversationExists($id) )
        {
            throw new ConversationNotFoundException('Conversation not found');
        }

        return Conversation::with('messages.sender')->find($id);
    }

    /**
     * Is the conversation one of the current User's conversations?
     *
     * @param $id
     * @throws ConversationNotFoundException
     * @return bool
     */
    public function isConversationExists($id)
    {
        $convIds = $this->userConversationIds($this->currentUser);

        if( ! in_array($id, $convIds))
        {
            return false;
        }

        return true;
    }

    /**
     * Get the common conversation id
     *
     * @param User $user
     * @param User $otherUser
     * @return mixed
     */
    public function getConversationIdBetween(User $user, User $otherUser)
    {
        $currentUserConvIds= $this->userConversationIds($user);
        $otherUserConvIds = $this->userConversationIds($otherUser);

        //returns an array containing all the values of $convsIds[0] that are present in all the $convsIds array.
        $matches = array_intersect($currentUserConvIds, $otherUserConvIds);

        $convId = $this->getSingleValueInArray($matches);

        return $convId;
    }

    /**
     * Get the single value contained in the array regardless of the key
     *
     * @param $array
     * @return mixed
     */
    protected  function getSingleValueInArray($array)
    {
        $array = array_values($array);

        if ( $array )
        {
            return $array[0];
        }
    }

    /**
     * Is the conversation shown for the current user
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function isShown(Conversation $conversation)
    {
        $user = $this->currentUser;

        return $this->isShownFor($user, $conversation);
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
        $hiddenConvs = $this->getHiddenConvs($user);

        $shown = ! in_array( $conversation->id, $hiddenConvs);

        return $shown;
    }

    /**
     * Get user's hidden conversation Ids
     *
     * @param User $user
     * @return array
     */
    public function getHiddenConvs(User $user)
    {
        $hiddenConvs = [];
        foreach($user->conversations as $conv)
        {
            if($conv->pivot->hidden == true)
            {
                $hiddenConvs[] = $conv->id;
            }
        }

        return $hiddenConvs;
    }

    /**
     * Get all conversation Ids of the user
     *
     * @internal param $conversations
     * @param User $user
     * @return array
     */
    protected function userConversationIds(User $user)
    {
        $conversations = $user->conversations()->get();

        $conversationIds = [];
        foreach($conversations as $conversation)
        {
            $conversationIds[] = $conversation->id;
        }

        return $conversationIds;
    }

    /**
     * Get the last shown conversation for the user
     *
     * @return mixed
     */
    public function getLastConversation()
    {
        $convs = $this->currentUser->conversations()->with('messages.sender')->get()->sortByDesc('updated_at');

        //the next conversation always will be the latest one cause we grabbed all conversations with latest() method
        foreach($convs as $conv)
        {
            if( $this->isShown($conv))
            {
                return $conv;
            }
        }
    }

    /**
     * Set hidden field to true for the conversation
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function setHiddenFor(Conversation $conversation)
    {
        $this->currentUser->conversations()->updateExistingPivot($conversation->id, ['hidden' => true,
                                                                                     'hidden_date' => Carbon::now()]);

        return true;
    }

    /**
     * Set hidden field to false for the conversation
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function setShownFor(Conversation $conversation)
    {
        $this->currentUser->conversations()->updateExistingPivot($conversation->id, ['hidden' => false]);

        return true;
    }

    /**
     * Create a conversation with the user
     *
     * @param User $user
     */
    public function createConversationWith(User $user)
    {
        $conversation = Conversation::create([]);

        $conversation->users()->attach($this->currentUser);
        $conversation->users()->attach($user);

        return $conversation;
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
            $shown = $this->isShownFor($user, $conversation);

            if( $shown )
            {
                return true;
            }
        }

        return false;
    }

    public function getAllShown()
    {
        //get the conversations with messages and sender to minimize mysql queries
        $convs = $this->currentUser->conversations()->with('messages.sender', 'users')->get()->sortByDesc('updated_at');

        $shownConvs = [];
        foreach($convs as $conv)
        {
            $shown = $this->isShown($conv);

            if ( $shown )
            {
                $shownConvs[] = $conv;
            }
        }

        return $shownConvs;
    }
}