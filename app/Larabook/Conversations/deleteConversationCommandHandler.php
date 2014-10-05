<?php namespace Larabook\Conversations;

use Illuminate\Support\Facades\Auth;
use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;

class deleteConversationCommandHandler implements CommandHandler {

    /**
     * @var ConversationRepository
     */
    private $conversationRepository;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(ConversationRepository $conversationRepository, UserRepository $userRepository)
    {
        $this->conversationRepository = $conversationRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Handle the command.
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        $otherUser = $this->userRepository->findByUsername($command->otherUsername);

        $conversation = $this->conversationRepository->getConversationWith($otherUser);

        $user = Auth::user();

        foreach ($user->conversations as $conv)
        {
            if ($conv->id == $conversation->id)
            {
                $conv->pivot->hidden = true;
                $conv->pivot->save();
            }
        }

        if($conversation->users->count() == 0) $conversation->forceDelete();

        $conversation = $this->conversationRepository->getLastConversation();

        return $conversation;
    }

}