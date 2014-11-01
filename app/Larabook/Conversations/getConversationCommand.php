<?php namespace Larabook\Conversations;

class getConversationCommand {

    /**
     * @var string
     */
    public $conversationId;

    /**
     * @param string sendToUsername
     */
    public function __construct($conversationId)
    {
        $this->conversationId = $conversationId;
    }

}