<?php namespace Larabook\Conversations;

use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;

class deleteConversationCommandHandler implements CommandHandler {

    /*
     * @var ConversationRepository
     */
    private $conversationRepo;
    /**
     * @var UserRepository
     */
    private $userRepo;

    public function __construct(ConversationRepository $conversationRepository, UserRepository $userRepo)
    {
        $this->conversationRepo = $conversationRepository;
        $this->userRepo = $userRepo;
    }

    /**
     * Hide or delete the conversation
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        $conversation = $this->conversationRepo->findByIdOrFail($command->convToDelete);

        //set hidden attribute to the conversation
        $this->conversationRepo->setHiddenFor($conversation);

        //delete the conversation if the conversation is not seen by anybody
        if( ! $this->conversationRepo->seenBySomebody($conversation) )
        {
            $conversation->delete();
        }
    }
}