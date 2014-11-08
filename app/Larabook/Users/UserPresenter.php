<?php namespace Larabook\Users;

use Illuminate\Support\Facades\Auth;
use Laracasts\Presenter\Presenter;

class UserPresenter extends Presenter {

    /**
     * Present a link to the user's gravatar
     *
     * @param int $size
     * @return string
     */
    public function gravatar($size = 30)
    {
        $email = md5($this->email);

        return "https://www.gravatar.com/avatar/{$email}?s={$size}";
    }

    public function profileUrl()
    {
        return "@".$this->entity->username;
    }

    public function followerCount()
    {
        $count = $this->entity->followers()->count();
        $plural = str_plural('Follower', $count);

        return "{$count} {$plural}";
    }

    public function statusCount()
    {
        $statusCount = $this->entity->statuses()->count();

        $plural = str_plural('Status',$statusCount);

        return "{$statusCount} {$plural}";
    }

    /**
     * If sender is the current user instead of username show "Me"
     */
    public function username()
    {
        if( $this->entity->username == Auth::user()->username )
        {
           return "Me" ;
        }

        return $this->entity->username;
    }
}