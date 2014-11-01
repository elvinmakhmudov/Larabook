<?php namespace Larabook\Conversations;

class ConversationPreview {

    public $id;

    public $otherUser;

    public $content;

    public $sender;

    function __construct($sender, $otherUser, $content, $id)
    {
        $this->sender = $sender;
        $this->otherUser = $otherUser;
        $this->content = $content;
        $this->id = $id;
    }
}