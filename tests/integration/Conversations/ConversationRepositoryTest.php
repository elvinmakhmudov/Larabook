<?php


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

    /** @test */
    public function it_gets_conversation_with_a_user()
    {
        $users = TestDummy::times(2)->create('Larabook\Users\User');

        $conversation = TestDummy::create('Larabook\Conversations\Conversation');

        $message= TestDummy::create('Larabook\Messages\Message',[
            'user_id' => $users[0]->id,
            'conversation_id' => $conversation->id,
            'content' => 'How are you?'
        ]);

        $conversation->users()->attach($users[0]->id);
        $conversation->users()->attach($users[1]->id);

        $gotConversation = $this->repo->getConversationBetween($users[0], $users['1']);

        $this->assertEquals($conversation->id, $gotConversation->id);

        $this->assertEquals($conversation->messages()->first()->content, $message->content);
    }

}
