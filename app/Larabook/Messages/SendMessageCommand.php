<?php namespace Larabook\Messages;

class SendMessageCommand {

    public $sendTo;
    public $message;

    /**
     * @param $message
     * @param $sendTo
     */
    function __construct($sendTo, $message)
    {
        $this->sendTo = $sendTo;
        $this->message = $message;
    }

}