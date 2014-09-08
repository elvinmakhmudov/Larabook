<?php namespace Larabook\Users;


class UserRepository {

    /**
     * Persist a user
     * @param User $user
     * @return mixed
     */
    public function save(User $user)
    {
        return $user->save();
    }

    /**
     * List of all users.
     * @param int $howMany
     * @return mixed
     */
    public function getPaginated($howMany = 25)
    {
        return User::simplePaginate($howMany);
    }
} 