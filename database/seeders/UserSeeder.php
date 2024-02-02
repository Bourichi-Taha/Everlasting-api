<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    $user = User::firstOrCreate(
        ['email' => 'user@example.com', 'username' => 'user'],
        ['password' => bcrypt('user')],
    );
    $user->assignRole('user');
    $admin = User::firstOrCreate(
        ['email' => 'admin@example.com', 'username' => 'admin'],
        ['password' => bcrypt('admin')],
    );
    $admin->assignRole('admin');
  }
}
