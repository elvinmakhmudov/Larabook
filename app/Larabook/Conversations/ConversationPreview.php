<?php namespace Larabook\Conversations;

class ConversationPreview {

    public $otherUser;

    public $content;

    public $sender;

    function __construct($sender, $otherUser, $content)
    {
        $this->sender = $sender;
        $this->otherUser = $otherUser;
        $this->content = $content;
    }
}