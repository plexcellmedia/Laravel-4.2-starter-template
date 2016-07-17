<?php

class UserTableSeeder extends Seeder
{

	public function run()
	{
		DB::table('users')->truncate();
		DB::table('users')->insert(
			array(
				'email'    => 'user@laravel.com',
				'password' => Hash::make('password')
			)
		);
	}

}