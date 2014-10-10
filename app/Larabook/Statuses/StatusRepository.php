<?php  namespace Larabook\Statuses;

use Illuminate\Support\Facades\Auth;
use Larabook\Users\User;

class StatusRepository {

    public $currentUser;

    public function __construct()
    {
        $this->currentUser = Auth::user();
    }

    /**
     * Save a new status for a user
     *
     * @param Status $status
     * @param $userId
     */
    public function save(Status $status, $userId)
    {
        return User::findOrFail($userId)
            ->statuses()
            ->save($status);
    }

    /**
     * Get the feed for a user.
     *
     * @return mixed
     */
    public function getFeed()
    {
        $userIds = $this->currentUser->followedUsers()->lists('followed_id');
        //to show own statuses
        $userIds[] = $this->currentUser->id;

        return Status::whereIn('user_id', $userIds)->latest()->with('user')->get();
    }
} 