<?php  namespace Larabook\Conversations\Actions; 

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Conversation;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\User;

class Get {

    public $currentUser;

    public function __construct($user = null)
    {
        $this->currentUser = $user ? $user : Auth::user();
    }

    /**
     * Get conversation between users
     *
     * @param $users
     * @return array
     */
    public function with($users)
    {
        //TODO::write another function using query builder to improve performance
        //add the current user to the end of the users array
        array_push($users, $this->currentUser);

        //delete duplicated users
        $users = array_unique($users);

        //reverse so that first item will be the current user
        $users = array_reverse($users);

        return $this->between($users);
    }
    
    /**
     * Get conversation between users
     *
     * @param $users
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function between($users)
    {
        $convId = $this->idBetween($users);

        return $this->byIdOrFail($convId);
    }

    /**
     * Get the conversation by Id or throw an exception
     *
     * @param $id
     * @return mixed
     * @throws ConversationNotFoundException
     */
    public function byIdOrFail($id)
    {
        $conversation = $this->byId($id);

        if( $conversation !== null) return $conversation;

        throw new ConversationNotFoundException;
    }
    
    /**
     * Find a conversation by its id
     *
     * @param $id
     * @return mixed
     */
    public function byId($id)
    {
        return Conversation::find($id);
    }
    
    /**
     * Get the common conversation id
     *
     * @return mixed
     */
    public function idBetween($users)
    {
        $filteredConvsArray = [];
        foreach ($users as $user)
        {
            //get user conversations
            $userConvs = $this->userConvs($user);

            //filter conversations by count to prevent getting false conversations
            $filteredConvsArray[] = $this->filterByCount($userConvs, count($users));
        }

        $matches = $this->intersectConversations($filteredConvsArray);

        return $this->singleValueInArray($matches);
    }

    /**
     * Get user's conversations
     *
     * @param User $user
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function userConvs(User $user)
    {
        $conversations = $user->conversations()->get();

        if( ! $conversations->isEmpty() ) return $conversations;

        throw new ConversationNotFoundException;
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
     * Intersect conversations by their Id
     *
     * @param $conversationsArray
     * @return mixed
     */
    public function intersectConversations($conversationsArray)
    {
        $idsArray = [];
        foreach ($conversationsArray as $conversations)
        {
            $idsArray[] = $this->idsOf($conversations);
        }

        //if 1 conversation was found return the array
        if(count($idsArray) == 1) return $idsArray[0];

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
    public function idsOf($conversations)
    {
        $ids = [];
        foreach ($conversations as $conversation)
        {
            $ids[] = $conversation->id;
        }

        return $ids;
    }
    
    /**
     * Get the single value contained in the array regardless of the key
     *
     * @param $array
     * @return mixed
     */
    protected  function singleValueInArray($array)
    {
        $array = array_values($array);

        if ( $array )
        {
            return $array[0];
        }
    }

    /**
     * Get user's hidden conversation Ids
     *
     * @param User $user
     * @return array
     */
    public function hiddenConvs(User $user)
    {
        return $user->conversations->filter(function($conv) {
            if($conv->pivot->hidden == true) {
                return true;
            }
        });
    }

    /**
     * Get the last shown conversation for the user
     *
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function last()
    {
        return $this->userConvs($this->currentUser)->sortByDesc('updated_at')->first();
    }

    /**
     * Get the users of the conversation
     *
     * @param Conversation $conversation
     * @return
     */
    public function users(Conversation $conversation)
    {
        return $conversation->users()->get();
    }

    /**
     * Get hidden date for the conversation
     *
     * @param Conversation $conversation
     * @return mixed
     */
    public function hiddenDate(Conversation $conversation)
    {
        return $hiddenDate = $this->currentUser->conversations()->find($conversation->id)->hidden_date;
    }

    /**
     * Get the unread conversations
     *
     * @return array
     */
    public function unreadConvs()
    {
        return $this->currentUser->conversations->filter(function($conv) {
            if( $conv->pivot->unread == true ) {
                return true;
            }
        });
    }
}