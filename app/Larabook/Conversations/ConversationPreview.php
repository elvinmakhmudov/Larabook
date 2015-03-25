<?php namespace Larabook\Conversations;

use Laracasts\Presenter\PresentableTrait;

class ConversationPreview {

    use PresentableTrait;

    /**
     * Path to the presenter for a preview
     * @var string
     */
    protected $presenter = 'Larabook\Conversations\Previews\PreviewPresenter';

    public $id;

    public $users;

    public $content;

    public $sender;
    public $unread;

    function __construct($sender, $users, $content, $id, $unread)
    {
        $this->sender = $sender;
        $this->users = $users;
        $this->content = $content;
        $this->id = $id;
        $this->unread = $unread;
    }
}