<?php namespace Larabook\Conversations;

use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;

class getConversationCommandHandler implements CommandHandler {

    public $userRepository;
    /**
     * @var
     */
    private $conversationRepository;

    public function __construct(UserRepository $userRepository, ConversationRepository $conversationRepository)
    {
        $this->userRepository = $userRepository;
        $this->conversationRepository = $conversationRepository;
    }

    /**
     * Handle the command.
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        try
        {
            $conversation = $this->conversationRepository->findAndCheck($command->conversationId);
        }
        catch(ConversationNotFoundException $e)
        {
            //if the conversation not found get the last conversation
            $conversation = $this->conversationRepository->getLastConversation();
        }

        return $conversation;
    }
}