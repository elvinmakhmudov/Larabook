<?php namespace Larabook\Conversations;

use Illuminate\Support\Facades\Auth;
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
        try {
            //get the user by username
            $otherUser = $this->userRepository->findbyUsername($command->sendToUsername);

            //get the conversation between users
            $mainConv = $this->conversationRepository->getConversationBetween(Auth::user(), $otherUser);

        } catch(UserNotFoundException $e) {

            //if the user not found get the last conversation
            $mainConv = $this->conversationRepository->getLastConversation(Auth::user());
        } catch(ConversationNotFoundException $e) {

            //if the conversation not found get the last conversation
            $mainConv = $this->conversationRepository->getLastConversation(Auth::user());
        }

        return $mainConv;

    }

}