<?php  namespace Larabook\Conversations;

use Illuminate\Support\Facades\Auth;
use Larabook\Users\User;

class ConversationRepository {

    /**
     * Get all samples of the user's conversations
     *
     * @param User $user
     * @return array
     */
    public function getPreviews(User $user)
    {
        //get the conversations with messages and sender to minimize mysql queries
        $convs = $user->conversations()->with('messages.sender', 'users')->get();

        $samples = [];
        foreach($convs as $conv)
        {
            //first() method because in the messages relationship we get the messages with latest() method
            $lastMessage = $conv->messages->first();

            $otherUsername = $this->getOtherUserInConversation($conv, $user);

            //TODO::bad code
            $previews[] = new ConversationPreview($lastMessage->sender->username, $otherUsername, $lastMessage->content);
        }

        return $previews;
    }
    /**
     * Get conversation id of the conversation between users
     *
     * @internal param User $user
     * @internal param User $otherUser
     * @param User $otherUser
     * @return array
     */
    public function getConversationWith(User $otherUser)
    {
        //get the current user
        $user = Auth::user();

        $userConvIds = $this->userConversationIds($user);
        $otherUserConvIds = $this->userConversationIds($otherUser);

        //returns an array containing all the values of $convsIds[0] that are present in all the $convsIds array.
        $matches = array_intersect($userConvIds, $otherUserConvIds);

        $convId = $this->getSingleValueInArray($matches);

        return Conversation::with('messages.sender')->findOrFail($convId);
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
        $conversations = $user->conversations;

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
     * @param User $currentUser
     * @return mixed
    @internal param User $user
     */
    public function getOtherUserInConversation(Conversation $conversation, User $currentUser)
    {
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

    public function getLastConversationFor(User $user)
    {
        //first() method because in the conversations relationship we get the conversations with latest() method
        return $user->conversations()->with('messages.sender')->first();
    }
}