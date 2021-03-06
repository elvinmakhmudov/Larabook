<?php namespace Larabook\Conversations\Previews;

use Illuminate\Support\Facades\Paginator;
use Larabook\Conversations\ConversationRepository;
use Larabook\Conversations\PreviewRepository;
use Laracasts\Commander\CommandHandler;

class getPreviewsCommandHandler implements CommandHandler {

    public $previewRepo;

    public $conversationRepo;

    public function __construct(PreviewRepository $previewRepo, ConversationRepository $conversationRepo)
    {
        $this->previewRepo = $previewRepo;
        $this->conversationRepo = $conversationRepo;
    }
    /**
     * Handle the command.
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        $conversations = $this->conversationRepo->getPaginatedShown(6);

        $previews = $this->previewRepo->getPreviewsOf($conversations);

        return $previews;
    }

}