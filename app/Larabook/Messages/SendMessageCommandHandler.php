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
        //get the user
        $user = $this->userRepo->findByUsername($command->sendTo);

        //get the proper conversation with the user
        $conversation = $this->getProperConversationWith($user);

        //send the message to the conversation
        $this->sendMessage($command->message, $conversation);
    }

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
     * @param User $sendToUser
     * @return array
     */
    public function getProperConversationWith(User $sendToUser)
    {
        //check if conversation already exist
        try
        {
            $conversation = $this->conversationRepo->getConversationWith($sendToUser);
        }
        catch(ConversationNotFoundException $e)
        {
            //if conversation does not exist create one and attach the users to its table

            $conversation = $this->conversationRepo->createConversationWith($sendToUser);
        }

        return $conversation;
    }

}