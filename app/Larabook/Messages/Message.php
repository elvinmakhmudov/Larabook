<?php namespace Larabook\Messages;

class Message extends \Eloquent {
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
}