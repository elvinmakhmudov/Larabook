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

    public function getAllForUser($userId)
    {
        return Status::whereUserId($userId)->get();
    }
} 