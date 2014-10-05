<?php namespace Larabook\Conversations;

use Larabook\Users\User;

class deleteConversationCommand {

    public $otherUsername;

    public function __construct($otherUsername)
    {
        $this->otherUsername = $otherUsername;
    }

}