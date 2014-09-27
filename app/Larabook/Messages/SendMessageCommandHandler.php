<?php namespace Larabook\Messages;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Larabook\Conversations\Conversation;
use Larabook\Conversations\ConversationRepository;
use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;

class SendMessageCommandHandler implements CommandHandler {

    public $userRepository;
    /**
     * @var ConversationRepository
     */
    private $conversationRepository;

    function __construct(UserRepository $userRepository, ConversationRepository $conversationRepository)
    {
        $this->userRepository = $userRepository;
        $this->conversationRepository = $conversationRepository;
    }

    /**
     * Send a message command handler
     *
     * @param object $command
     * @return void
     */
    public function handle($command)
    {
        $sendToUser = $this->userRepository->findByUsername($command->sendTo);

        try {

            $conversation = $this->conversationRepository->getConversationWith($sendToUser);

        } catch(ModelNotFoundException $e){
            //if conversation does not exist create one and attach the users

            $conversation = Conversation::create([]);

            $conversation->users()->attach($command->userId);
            $conversation->users()->attach($sendToUser->id);

        }

        //TODO::create send method in Message model that will create a message and raise an event using EventGenerator
        $message = Message::create([
            'user_id' => $command->userId,
            'conversation_id' => $conversation->id,
            'content' => $command->message
        ]);

    }

}