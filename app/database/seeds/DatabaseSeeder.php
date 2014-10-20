<?php


use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {

    /**
     * @var array
     */
    public $seeders = [
        'UsersTableSeeder',
        'StatusesTableSeeder',
        'MessagesTableSeeder'
    ];

    /**
     * @var array
     */
    public $tables = [
        'users',
        'statuses',
        'messages'
    ];
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

        $this->cleanDatabase();

        foreach($this->seeders as $seed){
            $this->call($seed);
        }
	}

    /**
     *Clean out the database for a new seed generation.
     */
    private function cleanDatabase()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach($this->tables as $table){
            DB::table($table)->truncate();
        }
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }

}
