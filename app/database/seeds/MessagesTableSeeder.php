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

        for( $i=0 ; $i <10 ; $i++ )
        {
            $conversation = Conversation::create([]);

            $conversation->users()->attach($sender);
            $conversation->users()->attach($faker->randomElement($usersIds));

            for( $j=0 ; $j <10 ; $j++ )
            {
                $message = Message::create([
                    'user_id' => $sender->id,
                    'content' => $faker->sentence(),
                    'conversation_id' => $conversation->id,
                ]);
            }
        }
    }
}
