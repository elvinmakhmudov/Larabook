<?php namespace Larabook\Inbox;

class Message extends \Eloquent {
	protected $fillable = ['user_id', 'conversation_id', 'content'];

    /**
     * A message belongs to a Conversation
     * @return mixed
     */
    public function conversation()
    {
        return $this->belongsTo('Conversation');
    }

    public function sender()
    {
        return $this->belongsTo('Larabook\Users\User', 'user_id');
    }
}