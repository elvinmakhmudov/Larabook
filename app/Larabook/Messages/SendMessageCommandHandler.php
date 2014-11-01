<?php namespace Larabook\Messages;

use Larabook\Conversations\ConversationRepository;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\User;
use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;
use Laracasts\Commander\Events\DispatchableTrait;

class SendMessageCommandHandler implements CommandHandler {

    use DispatchableTrait;

    public $userRepository;
    /**
     * @var ConversationRepository
     */
    private $conversationRepo;
    /**
     * @var MessageRepository
     */
    private $messageRepo;
    /**
     * @var App
     */
    private $app;

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
     * @return array
     */
    public function getProperConversation($command)
    {
        //check if conversation already exist
        try
        {
            $conversation = $this->conversationRepo->findById($command->sendTo);
        }
        catch(ConversationNotFoundException $e)
        {
            //if the conversation wasn't found probably username was sent

            //get the user
            $user = $this->userRepo->findByUsername($command->sendTo);

            $conversation = $this->getOrCreateConversationWith($user);
        }

        return $conversation;
    }

    /**
     * Get or create conversation with the user
     *
     * @param User $user
     * @return array
     */
    public function getOrCreateConversationWith(User $user)
    {
        try
        {
            $conversation = $this->conversationRepo->getConversationWith($user);
        }
        catch(ConversationNotFoundException $e)
        {
            //create a new conversation with User
            $conversation = $this->conversationRepo->createConversationWith($user);
        }

        return $conversation;
    }

}