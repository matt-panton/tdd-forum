<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'matt_panton',
            'email' => 'matthew@vrve.co',
            'password' => bcrypt('password'),
            'confirmed' => true,
        ]);
    }
}
