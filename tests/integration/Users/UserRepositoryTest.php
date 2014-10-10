<?php


use Larabook\Users\UserRepository;
use Laracasts\TestDummy\Factory as TestDummy;

class UserRepositoryTest extends \Codeception\TestCase\Test
{
    public $repo;
    /**
    * @var \IntegrationTester
    */
    protected $tester;

    protected function _before()
    {
        $this->repo = new UserRepository();
    }

    /** @test */
    public function it_paginates_all_users()
    {
        TestDummy::times(4)->create('Larabook\Users\User');

        $results = $this->repo->getPaginated(2);

        $this->assertCount(2, $results);
        
    }

    /** @test */
    public function it_follows_another_user()
    {
        //given I have 2 users
        $users = TestDummy::times(2)->create('Larabook\Users\User');

        //and one user follows another user
        $this->repo->follow($users[1]->id, $users[0]);

        //then I should see that user in the list of those that $user[0] follows
        $this->assertCount(1, $users[0]->followedUsers);
    }

    /** @test */
    public function it_unfollows_another_user()
    {
        //given I have 2 users
        $users = TestDummy::times(2)->create('Larabook\Users\User');

        //and one user follows another user
        $this->repo->follow($users[1]->id, $users[0]);

        //when i unfollow that same user
        $this->repo->unfollow($users[1]->id, $users[0]);

        //then I should see that user in the list of those that $user[0] follows
        $this->assertCount(0, $users[0]->followedUsers);
    }
}