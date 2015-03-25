<?php

// Composer: "fzaninotto/faker": "v1.3.0"
use Faker\Factory as Faker;
use Larabook\Conversations\Conversation;
use Larabook\Messages\Message;
use Larabook\Users\User;

class MessagesTableSeeder extends Seeder {

    public function run()
    {
        $faker = Faker::create();
        $sender = User::where('username','elvin')->first();
        $usersIds = User::lists('id');
        $conversationCount = 10;
        $messagesCount = 10;

        for( $i=0 ; $i < $conversationCount ; $i++ )
        {
            $conversation = Conversation::create([]);

            $randomUser = $faker->randomElement($usersIds);

            $conversation->users()->attach($randomUser);
            $conversation->users()->attach($sender);

            for( $j=0 ; $j < $messagesCount ; $j++ )
            {
                $message = Message::create([
                    'user_id' => $faker->randomElement([$randomUser, $sender->id]),
                    'content' => $faker->sentence(),
                    'conversation_id' => $conversation->id,
                ]);
            }
        }
    }
}
