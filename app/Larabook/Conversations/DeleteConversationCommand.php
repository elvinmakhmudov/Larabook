<?php namespace Larabook\Conversations;

class DeleteConversationCommand {

    public $convToDelete;

    public function __construct($convToDelete)
    {
        $this->convToDelete = $convToDelete;
    }

}