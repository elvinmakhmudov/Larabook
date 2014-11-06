<?php  namespace Larabook\Conversations;

use Carbon\Carbon;
use Illuminate\Support\Collection;
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
     * @param $users
     * @return array
     */
    public function getConversationWith($users)
    {
        //add the current user to the end of the users array
        array_push($users, $this->currentUser);

        //reverse so that first item will be the current user
        $users = array_reverse($users);

        return $this->getConversationBetween($users);
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
     * Get conversation between users
     *
     * @param $users
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function getConversationBetween($users)
    {
        //if the users are not the same get the conversation
        if( ! $this->areTheSame($users) )
        {
            $convId = $this->getConversationIdBetween($users);

            return $this->findById($convId);
        }

        //if users are identical get the conversation with myself
        return $this->getConversationWithMyself();
    }

    /**
     * Are the given users the same users?
     *
     * @param $users
     * @return bool
     */
    public function areTheSame($users)
    {

        //how many times user is the same
        $same = 0;
        foreach ($users as $user)
        {
            if( $user->is($this->currentUser ))
            {
                $same++;
            }
        }

        //if user was the same 2 or more times and count of users greater than the value of same return false
        if($same >= 2)
        {
            if( count($users) > $same )
            {
                return false;
            }

            return true;
        }

        return false;
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

            //if first and last user of the conversation are the same
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
     * @throws ConversationIsHiddenException
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function findById($id)
    {
        //throw an exception if the conversation does not exist for the current user
        $this->doesConversationExistsOrFail($id);

        //grab the conversation
        $conversation = Conversation::find($id);

        //throw an exception if the conversation is hidden for the current user
        $this->isConversationShownOrFail($conversation);

        return $conversation;
    }

    /**
     * Check whether conversation exists, if not throw an Exception
     *
     * @param $id
     * @return bool
     * @throws ConversationNotFoundException
     */
    public function doesConversationExistsOrFail($id)
    {
        if( ! $this->doesConversationExists($id) )
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
    public function isConversationShownOrFail($conversation)
    {
        if( ! $this->isShown($conversation))
        {
            throw new ConversationIsHiddenException('Conversation is hidden');
        }

        return true;
    }

    /**
     * Does the conversation one of the current User's conversations?
     *
     * @param $id
     * @return bool
     */
    public function doesConversationExists($id)
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
     * @return mixed
     */
    public function getConversationIdBetween($users)
    {
        $filteredConvsArray = [];
        foreach ($users as $user)
        {
            //get user conversations
            $userConvs = $this->getUserConvs($user);

            //filter conversations by count to prevent getting false conversations
            $filteredConvsArray[] = $this->filterByCount($userConvs, count($users));
        }

        $matches = $this->conversations_intersect($filteredConvsArray);

        $convId = $this->getSingleValueInArray($matches);

        return $convId;
    }

    /**
     * Intersect conversations by their Id
     *
     * @param $conversationsArray
     * @return mixed
     */
    public function conversations_intersect($conversationsArray)
    {
        $idsArray = [];
        foreach ($conversationsArray as $conversations)
        {
            $idsArray[] = $this->getIdsOf($conversations);
        }


        //returns an array containing all the values of $ids[0] that are present in all the $ids array.
        //the first value will be the main user's conversation and so on
        return call_user_func_array('array_intersect',$idsArray);
    }


    /**
     * Get conversations ids by conversations
     *
     * @param $conversations
     * @internal param $userConversations
     * @return array
     */
    public function getIdsOf($conversations)
    {
        $ids= [];
        foreach ($conversations as $conversation)
        {
            $ids[] = $conversation->id;
        }

        return $ids;
    }

    /**
     * Filter conversations by users count
     *
     * @param $conversations
     * @param $usersCount
     * @return array
     */
    public function filterByCount($conversations, $usersCount)
    {
        //TODO::$conversations is an array of the ids of the conversations not same conversations!!! change the function
        $filteredConvs = [];
        foreach ($conversations as $conversation)
        {
            if( $conversation->users_count == $usersCount )
            {
                $filteredConvs[] = $conversation;
            }
        }

        return $filteredConvs;
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
        $conversations = $this->getUserConvs($user);

        $conversationIds = [];
        foreach($conversations as $conversation)
        {
            $conversationIds[] = $conversation->id;
        }

        return $conversationIds;
    }

    /**
     * Get user's conversations
     *
     * @param User $user
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function getUserConvs(User $user)
    {
        $conversations = $user->conversations()->get();

        if( ! $conversations->isEmpty() ) return $conversations;

        throw new ConversationNotFoundException;
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
     */
    public function createConversationWith($users)
    {
        $users[] = $this->currentUser;

        return $this->createConversationBetween($users);
    }

    /*
     * Create Conversation between users
     *
     * @param $users
     * @return mixed
     */
    public function createConversationBetween($users)
    {
        $conversation = Conversation::create([]);

        return $this->attachUsersToConv($users, $conversation);
    }

    /**
     * Attach users to conversation
     *
     * @param $users
     * @param $conversation
     * @return \Larabook\Conversations\Conversation
     */
    public function attachUsersToConv($users, Conversation $conversation)
    {
        foreach ($users as $user)
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
            if( $this->isShownFor($user, $conversation) )
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all shown conversations with messages, senders and users attached to conversations
     *
     * @return array
     */
    public function getAllShown()
    {
        //get the conversations with messages and sender to minimize mysql queries
        $convs = $this->currentUser->conversations()->with('messages.sender', 'users')->get()->sortByDesc('updated_at');

        return $this->getShownConvsOf($convs);
    }

    /**
     * Get shown conversation of the collection
     *
     * @param Collection $convs
     * @return array
     */
    public function getShownConvsOf(Collection $convs)
    {
        $shownConvs = [];
        foreach($convs as $conv)
        {
            //if the conversation is shown for the current user add it to the shown conversations
            if ( $this->isShown($conv) )
            {
                $shownConvs[] = $conv;
            }
        }

        return $shownConvs;
    }
}