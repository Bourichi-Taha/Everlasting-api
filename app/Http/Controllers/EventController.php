<?php

namespace App\Http\Controllers;

use App\Enums\StatusEnum;
use App\Models\Event;
use App\Models\Permission;
use App\Notifications\CancelEventNotification;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
      $request->merge(['statusName' => StatusEnum::UPCOMING]);
      return parent::createOne($request);
  }

  public function updateOne($id, Request $request)
  {
      $item = Event::find($id);
    if ($item->statusName != StatusEnum::UPCOMING) {
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
      $request->merge(['statusName' => StatusEnum::UPCOMING]);
      return parent::updateOne($id, $request);
  }
  public function getUserEvents(Request $request)
  {
    if (!$request->user()->hasPermission('events', 'read')) {
        return response()->json([
            'success' => false,
            'errors' => [__('common.permission_denied')]
        ]);
    }
      $user = $request->user();
      $events = Event::where('owner_id', $user->id)->whereNot('statusName', StatusEnum::CANCELED)->get();
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
    if (!$request->user()->hasPermission('events', 'read')) {
        return response()->json([
            'success' => false,
            'errors' => [__('common.permission_denied')]
        ]);
    }
      $events = $request->user()->events()->whereNot('statusName', StatusEnum::CANCELED)->get();
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
    if ($event->statusName === StatusEnum::CANCELED) {
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
    if ($event->statusName === StatusEnum::CANCELED) {
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
      $event->statusName = StatusEnum::CANCELED;
      $event->save();
      $permissions = Permission::where('name', 'events.' . $event->id . '.cancel')->orWhere('name', 'events.' . $event->id . '.update')->get();
      DB::table('users_permissions')->whereIn('permission_id', $permissions->pluck('id'))->delete();
      Permission::destroy($permissions->pluck('id'));
      return ['success' => true, 'message' => __('events.canceled')];
  }

  public function afterReadAll($items, $user)
  {
    if (!$user->hasRole('admin')) {
        return $items->filter(function ($event) {
            return in_array($event->statusName, [StatusEnum::TODAY, StatusEnum::UPCOMING]);
        })->values();
    }
      return $items;
  }
}
