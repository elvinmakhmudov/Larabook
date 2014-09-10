<?php  namespace Larabook\Users; 

class FollowUserCommand {


    public $userIdToFollow;

    public $userId;

    function __construct($userIdToFollow, $userId)
    {
        $this->userIdToFollow = $userIdToFollow;
        $this->userId = $userId;
    }
}