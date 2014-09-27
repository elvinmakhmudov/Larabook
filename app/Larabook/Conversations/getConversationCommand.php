<?php namespace Larabook\Conversations;

class getConversationCommand {

    /**
     * @var string
     */
    public $sendToUsername;

    /**
     * @param string sendToUsername
     */
    public function __construct($sendToUsername)
    {
        $this->sendToUsername = $sendToUsername;
    }

}