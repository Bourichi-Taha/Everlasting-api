<?php

namespace Database\Seeders;

use App\Models\Status;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
  public function run()
  {
      $upcomming = Status::firstOrCreate(
          ['name' => 'Upcoming'],
      );
      $today = Status::firstOrCreate(
          ['name' => 'Today'],
      );
      $passed = Status::firstOrCreate(
          ['name' => 'Passed'],
      );
      $cancelled = Status::firstOrCreate(
          ['name' => 'Canceled'],
      );
  }
}
