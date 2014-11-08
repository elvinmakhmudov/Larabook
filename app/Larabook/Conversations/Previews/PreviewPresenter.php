<?php  namespace Larabook\Conversations\Previews;

use Laracasts\Presenter\Presenter;

class PreviewPresenter extends Presenter{

    /**
     * Present other users separated with commas
     */
    public function otherUsers()
    {
        $users = $this->entity->users;

        //get the usernames
        $usernames = $users->map(function($user)
        {
            return $user->username;
        })->toArray();

        //if  user has not sent the message to itself
        if( count(array_unique($usernames)) !== 1 )
        {
            // remove from the array the current user's username
            if( ($key = array_search( $this->entity->sender->username, $usernames) ) !== false) {
                unset($usernames[$key]);
            }
        }

        return implode(', ', $usernames);
    }

} 