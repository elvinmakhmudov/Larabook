<?php namespace Larabook\Inbox;

use Larabook\Users\UserRepository;
use Laracasts\Commander\CommandHandler;

class SendMessageCommandHandler implements CommandHandler {

    public $userRepository;

    function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
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

        $conversation = Conversation::create([]);

        $conversation->users()->attach($command->userId);
        $conversation->users()->attach($sendToUser->id);

        $message = Message::create([
            'user_id' => $command->userId,
            'conversation_id' => $conversation->id,
            'content' => $command->message
        ]);

    }

}