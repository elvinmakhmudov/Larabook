<?php namespace Larabook\Conversations;

use Larabook\Conversations\Exceptions\ConversationNotFoundException;
use Larabook\Users\Exceptions\UserNotFoundException;
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
            //get the user by username
            $user = $this->userRepository->findbyUsername($command->sendToUsername);

            //get the conversation between users
            $conversation = $this->conversationRepository->getConversationWith($user);
        }
        catch(UserNotFoundException $e)
        {
            //if the user not found get the last conversation
            $conversation = $this->conversationRepository->getLastConversation();
        }
        catch(ConversationNotFoundException $e)
        {
            //if the conversation not found get the last conversation
            $conversation = $this->conversationRepository->getLastConversation();
        }

        return $conversation;
    }
}