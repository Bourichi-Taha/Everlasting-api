<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
  public function run()
  {
      //
      $sports = Category::firstOrCreate(
          ['name' => 'Sports'],
      );
      $dance = Category::firstOrCreate(
          ['name' => 'Dance'],
      );
      $tango = Category::firstOrCreate(
          ['name' => 'Tango'],
      );
      $afro = Category::firstOrCreate(
          ['name' => 'Afro'],
      );
      $test = Category::firstOrCreate(
          ['name' => 'IT'],
      );
  }
}
