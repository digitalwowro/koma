<?php

use App\User;
use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $password = 'blana';
        $key = app('encrypt')->generateEncryptionKey($password);
        $recoveryString = 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa';

        $user = User::create([
            'name' => 'Tester Manole',
            'email' => 'tester@manole.ro',
            'password' => bcrypt($password),
            'role' => User::ROLE_ADMIN,
            'public_key' => $key['publicKey'],
            'salt' => $key['salt'],
            'recovery_string' => '',
        ]);

        $user->recovery_string = app('encrypt')->recoveryString($recoveryString, $password, $user);
        $user->save();
    }
}
