<?php namespace Larabook\Conversations;

class Conversation extends \Eloquent {
	protected $fillable = [];

    /**
     * A conversation belongs to many users
     */
    public function users()
    {
        return $this->belongsToMany('Larabook\Users\User');
    }

    /**
     * A conversation has many messages
     */
    public function messages()
    {
        return $this->hasMany('Larabook\Messages\Message')->latest();
    }
}