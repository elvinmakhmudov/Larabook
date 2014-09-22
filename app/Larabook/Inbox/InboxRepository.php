<?php  namespace Larabook\Inbox; 

use Larabook\Users\User;

class InboxRepository {

    /**
     * Get conversation id of the conversation between users
     *
     * @internal param User $user
     * @internal param User $otherUser
     * @param User $user
     * @param User $otherUser
     * @return array
     */
    public function getConversationId(User $user, User $otherUser)
    {
        $userConvIds = $this->userConversationIds($user);
        $otherUserConvIds = $this->userConversationIds($otherUser);

        //returns an array containing all the values of $convsIds[0] that are present in all the $convsIds array.
        $matches = array_intersect($userConvIds, $otherUserConvIds);

        return $this->getSingleValueInArray($matches);
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
        $usersInConversation = $conversation->users->toArray();

        $usernames = [];
        foreach ($usersInConversation as $user) {
            $usernames[] = $user['username'];
        }

        // unset username of the current user
        if(( $key = array_search( $currentUser->username , $usernames)) !== false) {
            unset($usernames[$key]);
        }

        return $this->getSingleValueInArray($usernames);

    }


    /**
     * Get the single value that contained in the array
     *
     * @param $array
     * @return mixed
     */
    public function getSingleValueInArray($array)
    {
        $array = array_values($array);

        if ( $array )
        {
            return $array[0];
        }
    }
}