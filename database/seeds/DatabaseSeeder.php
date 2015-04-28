<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();
		for($i = 0; $i < 1000; $i++){
		echo "nice";
		\App\Yep::create([
			'latitude'=>1,
			'longitude'=>1
		]);
		echo "nice";
		// $this->call('UserTableSeeder');
		}
	}

}
