<?php namespace Larabook\Conversations;

class DeleteConversationCommand {

    public $otherUsername;

    public function __construct($otherUsername)
    {
        $this->otherUsername = $otherUsername;
    }

}