<?php namespace Larabook\Conversations;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
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

        try {
            //get the user by username
            $otherUser = $this->userRepository->findbyUsername($command->sendToUsername);

            //get the conversation between users
            $mainConv = $this->conversationRepository->getConversationWith($otherUser);

        } catch(ModelNotFoundException $e) {

            //if user not found get the last conversation
            $mainConv = $this->conversationRepository->getLastConversation();
        }

        return $mainConv;

    }

}