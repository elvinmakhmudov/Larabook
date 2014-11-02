<?php


use Larabook\Conversations\Conversation;
use Larabook\Conversations\ConversationRepository;
use Laracasts\TestDummy\Factory as TestDummy;

class ConversationRepositoryTest extends \Codeception\TestCase\Test
{
    public $repo;
    public $main;
    /**
     * @var \IntegrationTester
     */
    protected $tester;

    protected function _before()
    {

        $user = $this->tester->signIn();
        $this->repo = new ConversationRepository($user);

        $this->main = $this->mainSetup();
    }

    public function mainSetup()
    {
        $user = $this->repo->currentUser;
        $otherUser = TestDummy::create('Larabook\Users\User');

        $conversation = Conversation::create([]);

        $message = TestDummy::create('Larabook\Messages\Message',[
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
        $gotConversation = $this->repo->getConversationWith($this->main['otherUser']);

        $this->assertEquals($this->main['conversation']->id, $gotConversation->id);

        $this->assertEquals($this->main['conversation']->messages()->first()->content, $this->main['message']->content);
    }

    /** @test */
    public function it_gets_other_users_username()
    {
        $otherUserUsername = $this->main['conversation']->otherUserInConversation;

        $this->assertEquals($otherUserUsername, $this->main['otherUser']->username);
    }

    /*
     * TODO::move to previewrepositorytest
     */
    public function it_gets_conversation_previews()
    {
        $previews = $this->repo->getPreviews();

        foreach($previews as $preview)
        {
            $this->assertEquals($preview->sender, $this->main['message']->sender->username);

            $otherUserUsername = $this->main['conversation']->otherUserInConversation;
            $this->assertEquals($preview->otherUser, $otherUserUsername);

            $this->assertEquals($preview->content, $this->main['message']->content);
        }
    }

    /** @test */
    public function it_gets_the_last_conversation()
    {
        //wait a second to create another conversation
        sleep(1);

        $conversation = Conversation::create([]);

        $conversation->users()->attach($this->main['user']->id);

        $last = $this->repo->getLastConversation();

        $this->assertEquals($last->id, $conversation->id);
    }

    /** @test */
    public function it_sets_the_hidden_field_to_true_for_the_conversation()
    {
        //create another conversation to prove that setHiddenFor function is working for every conversation
        $conversation = Conversation::create([]);
        $conversation->users()->attach($this->main['user']->id);

        $success = $this->repo->setHiddenFor($this->main['conversation']);

        $this->assertTrue($success);
    }

    /** @test */
    public function it_creates_conversation_with_a_user()
    {
        $otherUser= TestDummy::create('Larabook\Users\User');

        $conversation = $this->repo->createConversationWith($otherUser);

        $this->assertEquals($conversation->users->count(), 2);
    }

    /** @test */
    public function it_shows_if_the_conversation_is_seen_by_somebody()
    {
        //change the hidden column for first user to true and save it
        $pivot = $this->main['user']->conversations()->first()->pivot;
        $pivot->hidden = true;
        $pivot->save();

        //change the hidden column for second user to true and save it
        $pivot2 = $this->main['otherUser']->conversations()->first()->pivot;
        $pivot2->hidden = true;
        $pivot2->save();

        $result = $this->repo->seenBySomebody($this->main['conversation']);

        $this->assertFalse($result);
    }

    /** @test */
    public function it_gets_all_shown_conversations_for_the_current_user()
    {
        $convs = $this->repo->getAllShown();

        //check to equality by id
        $this->assertEquals($convs[0]->id,$this->main['conversation']->id);
    }
}
