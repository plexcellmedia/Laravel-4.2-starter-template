<?php

class AdminTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('admins')->truncate();
		DB::table('admins')->insert(
			array(
				'email'    => 'admin@laravel.com',
				'password' => Hash::make('password')
			)
		);
	}

}