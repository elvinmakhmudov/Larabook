<?php namespace Larabook\Composers;

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\ConversationRepository;

class MessagesComposer
{

    /**
     * @var ConversationRepository
     */
    private $conversationRepository;

    public function __construct(ConversationRepository $conversationRepository)
    {
        $this->conversationRepository = $conversationRepository;
    }

    public function compose($view)
    {
        $view->with('unread', $this->getUnread());
    }

    /**
     * Get unread conversations count
     *
     * @return int
     */
    public function getUnread()
    {
        if (Auth::check())
        {
            $count = count($this->conversationRepository->getUnreadConvs());
        } else {
            $count = 0;
        }

        return $count;
    }
}