<?php namespace Larabook\Messages;

use Larabook\Conversations\ConversationRepository;
use Laracasts\Commander\CommandHandler;

class getMessagesCommandHandler implements CommandHandler {

    public $conversationRepo;
    /**
     * @var MessageRepository
     */
    private $messageRepo;

    public function __construct(ConversationRepository $conversationRepo, MessageRepository $messageRepo)
    {
        $this->conversationRepo = $conversationRepo;
        $this->messageRepo = $messageRepo;
    }
    /**
     * Handle the command.
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        $hiddenDate = $this->conversationRepo->getHiddenDate($command->conversation);

        $messages = $this->messageRepo->getPaginatedFor($command->conversation, $hiddenDate, 5);

        return $messages;
    }

}