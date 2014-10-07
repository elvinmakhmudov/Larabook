<?php namespace Larabook\Conversations;

use Illuminate\Support\Facades\Auth;

class Conversation extends \Eloquent {
	protected $fillable = [];

    /**
     * A conversation belongs to many users
     */
    public function users()
    {
        return $this->belongsToMany('Larabook\Users\User')->withPivot('hidden');
    }

    /**
     * A conversation has many messages
     */
    public function messages()
    {
        return $this->hasMany('Larabook\Messages\Message')->latest();
    }

    /**
     * Get the other user's username in the conversation that is not the current user's username
     *
     * @return mixed
    @internal param User $user
     */
    public function getOtherUserInConversationAttribute()
    {
        $currentUser = Auth::user();
        $usersInConversation = $this->users;

        //save user's usernames that are in the conversation to an array
        $usernames = [];
        foreach ($usersInConversation as $user) {
            $usernames[] = $user->username;
        }

        // remove from the array the current user's username
        if(( $key = array_search( $currentUser->username, $usernames)) !== false) {
            unset($usernames[$key]);
        }

        return $this->getSingleValueInArray($usernames);
    }


    /**
     * Get the single value contained in the array regardless of the key
     *
     * @param $array
     * @return mixed
     */
    protected  function getSingleValueInArray($array)
    {
        $array = array_values($array);

        if ( $array )
        {
            return $array[0];
        }
    }
}