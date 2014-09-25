<?php namespace Larabook\Messages;

class SendMessageCommand {

    public $userId;
    public $sendTo;
    public $message;

    /**
     * @param $message
     * @param $sendTo
     * @param $userId
     */
    function __construct($sendTo, $message, $userId)
    {
        $this->sendTo = $sendTo;
        $this->message = $message;
        $this->userId = $userId;
    }

}