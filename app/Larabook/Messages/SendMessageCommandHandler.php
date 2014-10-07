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
        $sendToUser = $this->userRepo->findByUsername($command->sendTo);

        $conversation = $this->getOrCreateConversationWith($sendToUser);

        $message = Message::send($command->message);

        //save the message in the conversation
        $this->messageRepo->save(
            $message,
            $conversation
        );

        $this->dispatchEventsFor($message);
    }

    /**
     * If conversation exist get that or create a new one
     *
     * @param User $sendToUser
     * @return array
     */
    public function getOrCreateConversationWith(User $sendToUser)
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