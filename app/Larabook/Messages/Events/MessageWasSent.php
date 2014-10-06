<?php  namespace Larabook\Messages\Events; 

class MessageWasSent {

    public $content;

    public function __construct($content)
    {
        $this->content = $content;
    }

} 