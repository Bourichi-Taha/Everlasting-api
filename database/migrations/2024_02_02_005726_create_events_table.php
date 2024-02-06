<?php

use App\Enums\StatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
  public function up()
  {
      Schema::create('events', function (Blueprint $table) {
          $table->id();
          $table->string('name');
          $table->text('description');
          $table->integer('max_num_participants');
          $table->date('date');
          $table->time('start_time');
          $table->time('end_time');
          $table->string('statusName')->default(StatusEnum::UPCOMING);
          $table->foreignId('image_id')->nullable()->constrained('uploads')->cascadeOnDelete();
          $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
          $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
          $table->foreignId('location_id')->constrained('locations')->cascadeOnDelete();
          $table->timestamps();
      });
  }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
  public function down()
  {
      Schema::dropIfExists('events');
  }
};
