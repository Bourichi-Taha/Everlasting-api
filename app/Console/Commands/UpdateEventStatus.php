<?php

namespace App\Console\Commands;

use App\Enums\StatusEnum;
use Illuminate\Console\Command;
use App\Models\Event;
use Carbon\Carbon;

class UpdateEventStatus extends Command
{
    protected $signature = 'events:update-status';
    protected $description = 'Update status of events from upcoming to today from Today to past';

  public function handle()
  {
      $currentDate = Carbon::now();
      $status_up = StatusEnum::UPCOMING;
      $status_to = StatusEnum::TODAY;
      $status_past = StatusEnum::PAST;
      // Update events from "upcoming" to "today"
      Event::where('statusName', $status_up)
          ->where('date', '<=', $currentDate->toDateString())
          ->update(['statusName' => $status_to]);

      // Update events from "today" to "past"
      Event::where('statusName', $status_to)
          ->where('date', '<', $currentDate->toDateString())
          ->update(['statusName' => $status_past]);

      $this->info('Event statuses updated successfully.');
  }
}
