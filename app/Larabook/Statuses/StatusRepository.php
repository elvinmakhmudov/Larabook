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
     * @param int $page
     * @return mixed
     */
    public function getFeed($page = 0)
    {
        //how many statuses per page
        $pageSize = 10;

        $from = $page * $pageSize;

        $userIds = $this->currentUser->followedUsers()->lists('followed_id');
        //to show own statuses
        $userIds[] = $this->currentUser->id;

        return Status::whereIn('user_id', $userIds)->latest()->skip($from)->take($pageSize)->with('user')->get();
    }

    /**
     * Get user statuses
     *
     * @param User $user
     * @return
     */
    public function getStatusesOf(User $user)
    {
        return Status::where('user_id', $user->id)->latest()->with('user')->get();
    }
} 