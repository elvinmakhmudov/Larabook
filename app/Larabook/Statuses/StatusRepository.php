<?php  namespace Larabook\Statuses;

use Larabook\Users\User;

class StatusRepository {

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
     * @param User $user
     * @return mixed
     */
    public function getFeedForUser(User $user)
    {
        $userIds = $user->followedUsers()->lists('followed_id');
        //to show own statuses
        $userIds[] = $user->id;

        return Status::whereIn('user_id', $userIds)->latest()->with('user')->get();
    }
} 