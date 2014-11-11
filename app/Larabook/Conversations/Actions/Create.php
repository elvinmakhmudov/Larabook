<?php  namespace Larabook\Conversations\Actions; 

use Illuminate\Support\Facades\Auth;
use Larabook\Conversations\Conversation;
use Larabook\Users\User;

class Create {

    public $currentUser;

    public function __construct($user = null)
    {
        $this->currentUser = $user ? $user : Auth::user();
    }

    /**
     * Create a conversation with the user
     *
     */
    public function with($users)
    {
        $users[] = $this->currentUser;

        //remove duplicate users from the array
        $users = array_unique($users);

        return $this->between($users);
    }

    /*
     * Create Conversation between users
     *
     * @param $users
     * @return mixed
     */
    public function between($users)
    {
        //create Conversation
        $conversation = Conversation::create([]);

        //add update users_count field to the count of the users
        $conversation->users_count = count($users);
        $conversation->save();

        return $this->attachUsersToConv($users, $conversation);
    }

    /**
     * Attach users to conversation
     *
     * @param $users
     * @param $conversation
     * @return \Larabook\Conversations\Conversation
     */
    public function attachUsersToConv($users, Conversation $conversation)
    {
        foreach ($users as $user)
        {
            $conversation->users()->attach($user);
        }

        return $conversation;
    }
}