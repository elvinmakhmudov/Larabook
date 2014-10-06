<?php namespace Larabook\Messages;

use Larabook\Messages\Events\MessageWasSent;
use Laracasts\Commander\Events\EventGenerator;

class Message extends \Eloquent {

    use EventGenerator;

	protected $fillable = ['user_id', 'conversation_id', 'content'];

    protected $touches = ['conversation'];

    /**
     * A message belongs to a Conversation
     * @return mixed
     */
    public function conversation()
    {
        return $this->belongsTo('Larabook\Conversations\Conversation');
    }

    public function sender()
    {
        return $this->belongsTo('Larabook\Users\User', 'user_id');
    }

    /**
     * Send a message
     *
     * @param $content
     * @return static
     */
    public static function send($content)
    {
        $message = new static(compact('content'));

        $message->raise(new MessageWasSent($content));

        return $message;
    }
}