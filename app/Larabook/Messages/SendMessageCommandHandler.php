<?php namespace Larabook\Messages;

use Larabook\Conversations\ConversationRepository;
use Larabook\Conversations\Exceptions\ConversationIsHiddenException;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\Exceptions\UserNotFoundException;
use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;
use Laracasts\Commander\Events\DispatchableTrait;

class SendMessageCommandHandler implements CommandHandler {

    use DispatchableTrait;

    public $userRepo;
    /**
     * @var ConversationRepository
     */
    private $conversationRepo;
    /**
     * @var MessageRepository
     */
    private $messageRepo;

    function __construct(UserRepository $userRepository, ConversationRepository $conversationRepository, MessageRepository $messageRepository)
    {
        $this->userRepo = $userRepository;
        $this->conversationRepo = $conversationRepository;
        $this->messageRepo = $messageRepository;
    }

    /**
     * Send a message command handler
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        //get the proper conversation with the user
        $conversation = $this->getProperConversation($command);

        //send the message to the conversation
        $this->sendMessage($command->message, $conversation);

        //set the conversation as read for the current User
        $this->conversationRepo->setRead($conversation);


    }

    /**
     * Send message to the conversation
     *
     * @param $message
     * @param $conversation
     */
    public function sendMessage($message, $conversation)
    {
        $message = Message::send($message);

        //save the message in the conversation
        $this->messageRepo->save(
            $message,
            $conversation
        );

        //dispatct events for the message
        $this->dispatchEventsFor($message);
    }

    /**
     * If conversation exist get that or create a new one
     *
     * @param $command
     * @return array
     */
    public function getProperConversation($command)
    {
        //check if conversation already exist
        try
        {
            $conversation = $this->conversationRepo->findAndCheck($command->sendTo);

            //get all conversation users
            $users = $this->conversationRepo->getConversationUsers($conversation);

            //set the unread field to true for all users so they can get messages
            foreach ($users as $user)
            {
                $this->conversationRepo->setUnread($user, $conversation);
            }
        }
        catch(ConversationNotFoundException $e)
        {
            //if the conversation wasn't found probably usernames have been sent as we are using the same 'send' method in inboxcontroller

            $users = $this->getUsersByUsernames($command->sendTo);

            $conversation = $this->getOrCreateConversationWith($users);
        }

        return $conversation;
    }

    /**
     * Get users that have been sent by their usernames
     *
     * @param $usernames
     * @return array
     * @throws UserNotFoundException
     */
    public function getUsersByUsernames($usernames)
    {
        $usernames = $this->sanitizeUsernames($usernames);

        return $this->userRepo->getByUsernames($usernames);
    }

    /**
     * Sanitize the usernames
     *
     * @param $usernames
     * @return array
     */
    public function sanitizeUsernames($usernames)
    {
        //split usernames string
        $usernames = explode(',', $usernames);

        //trim each username
        $newUsernames = [];
        foreach ($usernames as $username)
        {
            $newUsernames[] = trim($username);
        }

        //delete duplicate usernames and return
        return array_unique($newUsernames);
    }

    /**
     * Get or create conversation with the user
     *
     * @param $users
     * @return array
     */
    public function getOrCreateConversationWith($users)
    {
        try
        {
           //get the conversation
            $conversation = $this->conversationRepo->getWithAndCheck($users);

        }
        catch(ConversationNotFoundException $e)
        {
            //create a new conversation with User
            $conversation = $this->conversationRepo->createConversationWith($users);
        }
        catch(ConversationIsHiddenException $e)
        {
            //conversation has been send as a parameter to the exception
            $conversation = $e->conversation;

            //get all conversation users
            $users = $this->conversationRepo->getConversationUsers($conversation);

            //set the hidden field to false for all users so they can get messages
            foreach ($users as $user)
            {
                $this->conversationRepo->setShownFor($user, $conversation);
                $this->conversationRepo->setUnread($user, $conversation);
            }
        }

        return $conversation;
    }
}