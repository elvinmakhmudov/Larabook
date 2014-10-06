<?php namespace Larabook\Messages;

use Larabook\Conversations\Conversation;
use Larabook\Conversations\ConversationRepository;
use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;
use Laracasts\Commander\Events\DispatchableTrait;

class SendMessageCommandHandler implements CommandHandler {

    use DispatchableTrait;

    public $userRepository;
    /**
     * @var ConversationRepository
     */
    private $conversationRepository;
    /**
     * @var MessageRepository
     */
    private $messageRepository;

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

        $conversation = $this->conversationRepo->getProperConversationWith($sendToUser);

        $message = Message::send($command->message);

        //save the message in the conversation
        $this->messageRepo->save(
            $message,
            $conversation
        );

        $this->dispatchEventsFor($message);

        return $message;
    }
}