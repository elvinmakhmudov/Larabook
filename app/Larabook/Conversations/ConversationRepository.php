<?php  namespace Larabook\Conversations;

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\User;

class ConversationRepository {

    /**
     * Get all samples of the user's conversations
     *
     * @return array
     */
    public function getPreviews()
    {
        //get the conversations with messages and sender to minimize mysql queries
        $convs = Auth::user()->conversations()->with('messages.sender', 'users')->get();

        $previews= [];
        foreach($convs as $conv)
        {
            //first() method because in the messages relationship we get the messages with latest() method
            $lastMessage = $conv->messages->first();

            $otherUsername = $this->getOtherUserInConversation($conv);

            //TODO::bad code
            $previews[] = new ConversationPreview($lastMessage->sender->username, $otherUsername, $lastMessage->content);
        }

        return $previews;
    }

    /**
     * Get conversation between users
     *
     * @param User $user
     * @param User $otherUser
     * @throws ConversationNotFoundException
     * @return array
     */
    public function getConversationBetween(User $user, User $otherUser)
    {

        $userConvIds = $this->userConversationIds($user);
        $otherUserConvIds = $this->userConversationIds($otherUser);

        //returns an array containing all the values of $convsIds[0] that are present in all the $convsIds array.
        $matches = array_intersect($userConvIds, $otherUserConvIds);

        $convId = $this->getSingleValueInArray($matches);

        $conversation = Conversation::with('messages.sender')->find($convId);


        //if not found throw an exception
        if( ! is_null($conversation)) return $conversation;

        throw new ConversationNotFoundException;
    }

    /**
     * Get all conversation Ids of the user
     *
     * @internal param $conversations
     * @param User $user
     * @return array
     */
    public function userConversationIds(User $user)
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
     * Get the other user's username in the conversation that is not the current user's username
     *
     * @param $conversation
     * @return mixed
    @internal param User $user
     */
    public function getOtherUserInConversation(Conversation $conversation)
    {
        $currentUser = Auth::user();
        $usersInConversation = $conversation->users;

        //save user's usernames that are in the conversation to an array
        $usernames = [];
        foreach ($usersInConversation as $user) {
            $usernames[] = $user->username;
        }

        // remove from the array the current user's username
        if(( $key = array_search( $currentUser->username , $usernames)) !== false) {
            unset($usernames[$key]);
        }

        return $this->getSingleValueInArray($usernames);
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

    public function getLastConversation()
    {
        //first() method because in the conversations relationship we get the conversations with latest() method
        return Auth::user()->conversations()->with('messages.sender')->first();
    }
}