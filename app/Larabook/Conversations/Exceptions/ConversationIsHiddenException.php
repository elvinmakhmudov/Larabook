<?php  namespace Larabook\Conversations\Exceptions; 

use Larabook\Conversations\Conversation;

class ConversationIsHiddenException extends \Exception{

    public $conversation;

    public function __construct($message, Conversation $conversation)
    {
        parent::__construct($message);

        $this->conversation = $conversation;
    }

} 