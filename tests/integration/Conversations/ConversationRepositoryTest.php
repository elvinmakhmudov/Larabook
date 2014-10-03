<?php


use Larabook\Conversations\Conversation;
use Larabook\Conversations\ConversationRepository;
use Laracasts\TestDummy\Factory as TestDummy;

class ConversationRepositoryTest extends \Codeception\TestCase\Test
{
    public $repo;
    /**
     * @var \IntegrationTester
     */
    protected $tester;

    protected function _before()
    {
        $this->repo = new ConversationRepository();
    }

    public function mainSetup()
    {
        $users = TestDummy::times(2)->create('Larabook\Users\User');

        $conversation = Conversation::create([]);

        $message= TestDummy::create('Larabook\Messages\Message',[
            'user_id' => $users[0]->id,
            'conversation_id' => $conversation->id
        ]);

        $conversation->users()->attach($users[0]->id);
        $conversation->users()->attach($users[1]->id);

        $result = [
            'users' => $users,
            'conversation' => $conversation,
            'message' => $message
        ];

        return $result;
    }

    //TODO::write another tests!!!!!
    /** @test */
    public function it_gets_conversation_between_users()
    {
        $main = $this->mainSetup();

        $gotConversation = $this->repo->getConversationBetween($main['users'][0], $main['users'][1]);

        $this->assertEquals($main['conversation']->id, $gotConversation->id);

        $this->assertEquals($main['conversation']->messages()->first()->content, $main['message']->content);
    }

    /** @test */
    public function it_gets_other_users_username()
    {
        $main = $this->mainSetup();

        $otherUserUsername = $this->repo->getOtherUserInConversation($main['conversation'], $main['users'][0]);

        $this->assertEquals($otherUserUsername, $main['users'][1]->username);
    }

    /** @test */
    public function it_gets_conversation_previews()
    {
        $main = $this->mainSetup();

        $previews = $this->repo->getPreviews($main['users'][0]);

        foreach($previews as $preview)
        {
            $this->assertEquals($preview->sender, $main['message']->sender->username);

            $otherUserUsername = $this->repo->getOtherUserInConversation($main['conversation'], $main['users'][0]);
            $this->assertEquals($preview->otherUser, $otherUserUsername);

            $this->assertEquals($preview->content, $main['message']->content);
        }
    }

    //TODO::find out why the test fails
    /** @test */
    public function it_gets_the_last_conversation()
    {
        $main = $this->mainSetup();

        //wait a second to create another conversation
        sleep(1);

        $conversation = Conversation::create([]);

        $conversation->users()->attach($main['users'][0]->id);

        $last = $this->repo->getLastConversation($main['users'][0]);

        $this->assertEquals($last->id, $conversation->id);
    }
}
