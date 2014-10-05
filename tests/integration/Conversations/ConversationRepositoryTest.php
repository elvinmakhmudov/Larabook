<?php


use Illuminate\Support\Facades\Auth;
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
        $this->repo = new ConversationRepository;

        $user = $this->tester->signIn();
        $this->repo->currentUser = $user;
    }

    public function mainSetup()
    {
        $user = $this->repo->currentUser;
        $otherUser= TestDummy::create('Larabook\Users\User');

        $conversation = Conversation::create([]);

        $message= TestDummy::create('Larabook\Messages\Message',[
            'user_id' => $user->id,
            'conversation_id' => $conversation->id
        ]);

        $conversation->users()->attach($user->id);
        $conversation->users()->attach($otherUser->id);

        $result = [
            'user' => $user,
            'otherUser' => $otherUser,
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

        $gotConversation = $this->repo->getConversationWith($main['otherUser']);

        $this->assertEquals($main['conversation']->id, $gotConversation->id);

        $this->assertEquals($main['conversation']->messages()->first()->content, $main['message']->content);
    }

    /** @test */
    public function it_gets_other_users_username()
    {
        $main = $this->mainSetup();

        $otherUserUsername = $main['conversation']->otherUserInConversation;

        $this->assertEquals($otherUserUsername, $main['otherUser']->username);
    }

    /** @test */
    public function it_gets_conversation_previews()
    {
        $main = $this->mainSetup();

        $previews = $this->repo->getPreviews();

        foreach($previews as $preview)
        {
            $this->assertEquals($preview->sender, $main['message']->sender->username);

            $otherUserUsername = $main['conversation']->otherUserInConversation;
            $this->assertEquals($preview->otherUser, $otherUserUsername);

            $this->assertEquals($preview->content, $main['message']->content);
        }
    }

    /** @test */
    public function it_gets_the_last_conversation()
    {
        $main = $this->mainSetup();

        //wait a second to create another conversation
        sleep(1);

        $conversation = Conversation::create([]);

        $conversation->users()->attach($main['user']->id);

        $last = $this->repo->getLastConversation();

        $this->assertEquals($last->id, $conversation->id);
    }
}
