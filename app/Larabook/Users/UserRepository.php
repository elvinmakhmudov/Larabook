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
     * Fetch a user by their username with his statuses.
     *
     * TODO::get rid of this function
     *
     * @param $username
     * @return mixed
     */
    public function findByUsernameWithStatuses($username)
    {
         return User::with('statuses.user')->whereUsername($username)->firstOrFail();
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

        throw new UserNotFoundException;
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