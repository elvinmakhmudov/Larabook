<?php namespace Larabook\Users;


use Larabook\Users\Exceptions\UserNotFoundException;

class UserRepository {

    /**
     * Persist a user
     *
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
        return User::orderBy('username', 'asc')->simplePaginate($howMany);
    }

    /**
     * Fetch a user by their username with their statuses.
     *
     * @param $username
     * @throws UserNotFoundException
     * @return mixed
     */
    public function findByUsername($username)
    {
        $user =  User::whereUsername($username)->first();

        if( ! is_null($user)) return $user;

        throw new UserNotFoundException('User ' . $username . ' was not found');
    }

    /**
     * Get by usernames
     *
     * @param $usernames
     * @throws UserNotFoundException
     * @return array
     */
    public function getByUsernames($usernames)
    {
        $users = [];
        foreach ($usernames as $username)
        {
            //get the user
            $users[] = $this->findByUsername($username);
        }

        return $users;
    }

    /**
     * Find a user by their Id
     *
     * @param $id
     * @return mixed
     */
    public function findById($id)
    {
        return User::findOrFail($id);
    }

    /**
     * Follow a Larabook user.
     *
     * @param $userIdToFollow
     * @param User $user
     * @return mixed
     */
    public function follow($userIdToFollow, User $user)
    {
        return $user->followedUsers()->attach($userIdToFollow);
    }

    /**
     * Unfollow a Larabook user.
     *
     * @param $userIdToUnfollow
     * @param User $user
     * @return mixed
     */
    public function unfollow($userIdToUnfollow, User $user)
    {
        return $user->followedUsers()->detach($userIdToUnfollow);
    }
}