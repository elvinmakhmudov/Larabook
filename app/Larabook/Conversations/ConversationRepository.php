<?php  namespace Larabook\Conversations;

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\User;

class ConversationRepository {

    public $currentUser;


    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    /**
     * Get all samples of the user's conversations
     *
     * @return array
     */
    public function getPreviews()
    {
        //get the conversations with messages and sender to minimize mysql queries
        $convs = $this->currentUser->conversations()->with('messages.sender', 'users')->get();

        $previews= [];
        foreach($convs as $conv)
        {
            //if the conversation is shown for the current user make a preview
            if($this->isShown($conv))
            {
                $previews[] = $this->makePreviewFor($conv);
            }
        }

        return $previews;
    }

    /**
     * Make a preview for the conversation
     *
     * @param Conversation $conv
     * @return \Larabook\Conversations\ConversationPreview
     */
    public function makePreviewFor(Conversation $conv)
    {
        //first() method because in the messages relationship we get the messages with latest() method
        $lastMessage = $conv->messages->first();

        $otherUsername = $conv->otherUserInConversation;

        //TODO::bad code
        $preview = new ConversationPreview($lastMessage->sender->username, $otherUsername, $lastMessage->content);

        return $preview;
    }

    /**
     * Get conversation between users
     *
     * @param User $otherUser
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
        $convId = $this->getCommonConversationId($user, $otherUser);

        $conversation = Conversation::with('messages.sender')->find($convId);

        if( ! is_null($conversation)) return $conversation;

        throw new ConversationNotFoundException;
    }

    /**
     * Get the common conversation id
     *
     * @param User $user
     * @param User $otherUser
     * @return mixed
     */
    public function getCommonConversationId(User $user, User $otherUser)
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
        $convs = $this->currentUser->conversations()->with('messages.sender')->get();

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
        foreach ($this->currentUser->conversations as $conv)
        {
            if ($conv->id == $conversation->id)
            {
                $conv->pivot->hidden = true;
                $conv->pivot->save();
                return true;
            }
        }
    }

    /**
     * Create a conversation with the user
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
     * If conversation exist get that or create a new one
     *
     * @param User $sendToUser
     * @return array
     */
    public function getProperConversationWith(User $sendToUser)
    {
        //check if conversation already exist
        try
        {
            $conversation = $this->getConversationWith($sendToUser);
        }
        catch(ConversationNotFoundException $e)
        {
            //if conversation does not exist create one and attach the users to its table

            $conversation = $this->createConversationWith($sendToUser);
        }

        return $conversation;
    }
}