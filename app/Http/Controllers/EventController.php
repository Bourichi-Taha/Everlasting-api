<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Permission;
use App\Models\Status;
use App\Notifications\CancelEventNotification;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Log;

class EventController extends CrudController
{
    //
    protected $table = 'events';
    protected $modelClass = Event::class;

  public function createOne(Request $request)
  {
      $owner_id = $request->user()->id;
      $date = $request->input('date');
      $carbonDate = Carbon::parse($date);
      $request->merge(['owner_id' => $owner_id]);
      $request->merge(['date' => $carbonDate]);
      $status = Status::where('name', 'Upcoming')->first();
    if ($status) {
        $request->merge(['status_id' => $status->id]);
    } else {
        $request->merge(['status_id' => 1]);
    }
      return parent::createOne($request);
  }
  public function readOne($id, Request $request)
  {

      return parent::readOne($id, $request);
  }

  public function updateOne($id, Request $request)
  {
      $item = Event::find($id);
    if ($item->statusName != 'Upcoming') {
        return response()->json([
            'success' => false,
            'errors' => [__('events.future_event')]
        ]);
    }
      $owner_id = $request->user()->id;
      $date = $request->input('date');
      $carbonDatetime = Carbon::parse($date);
      $request->merge(['owner_id' => $owner_id]);
      $request->merge(['date' => $carbonDatetime]);
      $status = Status::where('name', 'Upcoming')->first();
    if ($status) {
        $request->merge(['status_id' => $status->id]);
    } else {
        $request->merge(['status_id' => 1]);
    }
      return parent::updateOne($id, $request);
  }
  public function getUserEvents(Request $request)
  {
      $user = $request->user();
      $status = Status::where('name', 'Canceled')->first();
      Log::info($status->id);
      $events = Event::where('owner_id', $user->id)->whereNot('status_id', '=', $status->id)->get();
    if (!$events) {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.not_found')]
        ];
    }
      return [
          'success' => true,
          'data' => ['items' => $events],
      ];
  }
  public function getAllRegistered(Request $request)
  {
      $status = Status::where('name', 'Canceled')->first();
      $events = $request->user()->events()->whereNot('status_id', '=', $status->id)->get();
    if (!$events) {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.not_found')]
        ];
    }
      return [
          'success' => true,
          'data' => ['items' => $events],
      ];
  }
  public function registerToEvent(Request $request)
  {
      $userId = $request->user()->id;
      $event = Event::find($request->input('event_id'));
    if (!$event) {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.not_found')]
        ];
    }
    if ($event->statusName === 'Canceled') {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.canceled')]
        ];
    }
    if ($event->registeredNumber > $event->max_num_participants - 1) {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.filled')]
        ];
    }
      $event->syncUsers([...$event->registeredIds, $userId]);
      return ['success' => true, 'message' => __('events.register_success')];
  }
  public function unregisterToEvent(Request $request)
  {
      $userId = $request->user()->id;
      $event = Event::find($request->input('event_id'));
    if (!$event) {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.not_found')]
        ];
    }
    if ($event->statusName === 'Canceled') {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.canceled')]
        ];
    }
      $event->detachUsers([$userId]);
      return ['success' => true, 'message' => __('events.unregister_success')];
  }
  public function cancelEvent($id, Request $request)
  {
      $user = $request->user();
    if (!$user->hasPermission('events', 'cancel', $id)) {
        return response()->json([
            'success' => false,
            'errors' => [__('common.permission_denied')]
        ]);
    }
      $status = Status::where('name', 'Canceled')->first();
      $event = Event::find($id);
    if (!$event) {
        return [
            'success' => false,
            'errors' => [__(Str::of($this->table)->replace('_', '-') . '.not_found')]
        ];
    }

      // Update the attribute
      $users = $event->registereds()->get();
    foreach ($users as $user) {
        $user->notify(new CancelEventNotification($event->name, $user->username));
    }
      $event->status_id = $status->id;
      $event->save();
      $permissions = Permission::where('name', 'events.' . $event->id . '.cancel')->orWhere('name', 'events.' . $event->id . '.update')->get();
      DB::table('users_permissions')->whereIn('permission_id', $permissions->pluck('id'))->delete();
      Permission::destroy($permissions->pluck('id'));
      return ['success' => true, 'message' => __('events.canceled')];
  }
}
