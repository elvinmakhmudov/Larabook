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
        return $this->getConversationBetween($this->currentUser, $otherUser);
    }

    /**
     * Get hidden date for the conversation
     *
     * @param Conversation $conversation
     * @return mixed
     */
    public function getHiddenDate(Conversation $conversation)
    {
        return $hiddenDate = $this->currentUser->conversations()->find($conversation->id)->hidden_date;
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
        //if the users are not identical get the conversation
        if( ! $user->is($otherUser))
        {
            $convId = $this->getConversationIdBetween($user, $otherUser);

            return $this->findById($convId);
        }

        //if users are identical get the conversation with myself
        return $this->getConversationWithMyself();
    }

    /**
     * Get the conversation with myself
     *
     * @return mixed
     * @throws ConversationNotFoundException
     */
    public function getConversationWithMyself()
    {
        $conversations = $this->currentUser->conversations()->with('users')->get();

        foreach ($conversations as $conversation)
        {
            $users = $conversation->users;

            if( $users->first() == $users->last() )
            {
                return $conversation;
            }
        }

        throw new ConversationNotFoundException;
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

        $conversation = Conversation::find($id);

        return $conversation;
    }

    /**
     * Is the conversation one of the current User's conversations?
     *
     * @param $id
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
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function getLastConversation()
    {
        $convs = $this->currentUser->conversations()->get()->sortByDesc('updated_at');

        //the next conversation always will be the latest one cause we grabbed all conversations with latest() method
        foreach($convs as $conv)
        {
            if( $this->isShown($conv))
            {
                return $conv;
            }
        }

        throw new ConversationNotFoundException;
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

        //attach current user to the conversation
        $conversation->users()->attach($this->currentUser);

        //if other user is not the current user attach other user too
        if( ! $user->is($this->currentUser))
        {
            $conversation->users()->attach($user);
        }

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