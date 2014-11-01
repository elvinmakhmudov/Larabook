<?php namespace Larabook\Messages;

use Larabook\Conversations\Conversation;

class getMessagesCommand {

    public $conversation;

    function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

}