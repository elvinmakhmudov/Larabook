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

        //delete duplicated users
        $users = array_unique($users);

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
        $convId = $this->getConversationIdBetween($users);

        return $this->findByIdOrFail($convId);
    }

    /**
     * Find conversation by Id or throw proper exception
     *
     * @param $id
     * @throws ConversationIsHiddenException
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function findByIdOrFail($id)
    {
        //throw an exception if the conversation does not exist for the current user
        $this->doesConversationExistOrFail($id);

        //grab the conversation
        $conversation = $this->findById($id);

        //throw an exception if the conversation is hidden for the current user
        $this->isConversationShownOrFail($conversation);

        return $conversation;
    }

    /**
     * Find a conversation by its id
     *
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return Conversation::find($id);
    }

    /**
     * Check whether conversation exists, if not throw an Exception
     *
     * @param $id
     * @return bool
     * @throws ConversationNotFoundException
     */
    public function doesConversationExistOrFail($id)
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
            //send the hidden conversation as an argument
            throw new ConversationIsHiddenException('Conversation is hidden', $conversation);
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
        $conversations = $this->getUserConvs($this->currentUser);

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


        //returns an array containing all the values of $idsArray[0] that are present in all the $idsArray array.
        //the first value will be the main user's conversation and so on
        return call_user_func_array('array_intersect', $idsArray);
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
     * @throws ConversationNotFoundException
     * @return array
     */
    public function filterByCount($conversations, $usersCount)
    {
        $filteredConvs = [];
        foreach ($conversations as $conversation)
        {
            if( $conversation->users_count == $usersCount )
            {
                $filteredConvs[] = $conversation;
            }
        }

        //if any conversation is found return filtered conversations
        if( ! empty($filteredConvs)) return $filteredConvs;

        throw new ConversationNotFoundException;
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
     * Set hidden field of the conversation to false for the current user
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function setShown(Conversation $conversation)
    {
        return $this->setShownFor($this->currentUser, $conversation);
    }

    /**
     * Set hidden field of the conversation to false for the user
     *
     * @param User $user
     * @param Conversation $conversation
     * @return \Larabook\Conversations\Conversationn
     */
    public function setShownFor(User $user, Conversation $conversation)
    {
        $user->conversations()->updateExistingPivot($conversation->id, ['hidden' => false]);

        return true;
    }

    /**
     * Get the users of the conversation
     *
     * @param Conversation $conversation
     * @return
     */
    public function getConversationUsers(Conversation $conversation)
    {
        return $conversation->users()->get();
    }

    /**
     * Create a conversation with the user
     *
     */
    public function createConversationWith($users)
    {
        $users[] = $this->currentUser;

        //remove duplicate users from the array
        $users = array_unique($users);

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
        //create Conversation
        $conversation = Conversation::create([]);

        //add update users_count field to the count of the users
        $conversation->users_count = count($users);
        $conversation->save();

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