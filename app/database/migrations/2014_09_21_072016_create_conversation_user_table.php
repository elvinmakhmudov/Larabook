<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateConversationUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('conversation_user', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('conversation_id')->unsigned()->index();
			$table->foreign('conversation_id')->references('id')->on('conversations')->onDelete('cascade');
			$table->integer('user_id')->unsigned()->index();
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->boolean('unread')->default(true);
            $table->boolean('hidden');
            $table->timestamp('hidden_date');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('conversation_user');
	}

}
