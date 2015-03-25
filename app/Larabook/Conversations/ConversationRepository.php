<?php  namespace Larabook\Conversations;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Actions\Check;
use Larabook\Conversations\Actions\Create;
use Larabook\Conversations\Actions\Get;
use Larabook\Conversations\Actions\Set;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\User;

class ConversationRepository {

    public $currentUser;
    /**
     * @var Get
     */
    private $get;
    /**
     * @var Check
     */
    private $check;
    /**
     * @var Create
     */
    private $create;
    /**
     * @var Set
     */
    private $set;

    public function __construct($user = null, Get $get = null, Check $check = null, Create $create = null, Set $set = null)
    {
        $this->currentUser = $user ? $user : Auth::user();
        $this->get = $get ? $get : new Get($this->currentUser);
        $this->check = $check ? $check : new Check($this->currentUser);
        $this->create = $create ? $create : new Create($this->currentUser);
        $this->set = $set ? $set : new Set($this->currentUser);
    }

    /**
     * Get conversation between users
     *
     * @param $users
     * @return array
     */
    public function getWithAndCheck($users)
    {
        $conversation = $this->get->with($users);

        $this->check->shownOrFail($conversation);

        return $conversation;
    }

    /**
     * Find the conversation by it's id and check if it is shown
     *
     * @param $id
     * @return mixed
     * @throws ConversationIsHiddenException
     * @throws ConversationNotFoundException
     */
    public function findAndCheck($id)
    {
        $conversation = $this->get->byIdOrFail($id);

        if( ! $this->check->isShown($conversation))
        {
            //send the hidden conversation as an argument
            throw new ConversationIsHiddenException('Conversation is hidden', $conversation);
        }

        return $conversation;
    }

    /**
     * Get hidden date for the conversation
     *
     * @param Conversation $conversation
     * @return mixed
     */
    public function getHiddenDate(Conversation $conversation)
    {
        return $this->get->hiddenDate($conversation);
    }

    /**
     * Find a conversation by its id
     *
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return $this->get->byId($id);
    }



    /**
     * Get the last shown conversation for the user
     *
     * @throws ConversationNotFoundException
     * @return mixed
     */
    public function getLastConversation()
    {
        return $this->get->last();
    }

    /**
     * Set hidden field to true for the conversation
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function setHiddenFor(Conversation $conversation)
    {
        return $this->set->hiddenFor($conversation);
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
        return $this->set->shownFor($user, $conversation);
    }

    /**
     * Get the users of the conversation
     *
     * @param Conversation $conversation
     * @return
     */
    public function getConversationUsers(Conversation $conversation)
    {
        return $this->get->users($conversation);
    }

    /**
     * Create a conversation with the user
     *
     * @param $users
     * @return \Larabook\Conversations\Conversation
     */
    public function createConversationWith($users)
    {
        return $this->create->with($users);
    }

    /**
     * Can somebody see the conversation?
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function seenBySomebody(Conversation $conversation)
    {
        return $this->check->seenBySomebody($conversation);
    }

    /**
     * Get all shown conversations with messages, senders and users attached to conversations
     *
     * @param $howMany
     * @return array
     */
    public function getPaginatedShown($howMany = 10)
    {
        //get the conversations with messages and sender to minimize mysql queries
        $convs = $this->currentUser->conversations()->with('messages.sender', 'users')->paginate($howMany)->sortByDesc('updated_at');

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
            if ( $this->check->isShown($conv) )
            {
                $shownConvs[] = $conv;
            }
        }

        return $shownConvs;
    }


    /**
     * Set the unread field to false
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function setRead(Conversation $conversation)
    {
        return $this->set->read($conversation);
    }

    /**
     * Get the unread conversations
     *
     * @return mixed
     */
    public function getUnreadConvs()
    {
        return $this->get->unreadConvs();
    }

    /**
     * Is the given conversation unread by the current user
     *
     * @param Conversation $conversation
     * @return bool
     */
    public function isConvUnread(Conversation $conversation)
    {
        return $this->check->isUnread($conversation);
    }

    /**
     * Set the conversation as unread for the user
     *
     * @param User $user
     * @param Conversation $conversation
     */
    public function setUnread(User $user, Conversation $conversation)
    {
        $this->set->unread($user, $conversation);
    }
}