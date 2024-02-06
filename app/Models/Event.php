<?php

namespace App\Models;

use App\Notifications\CancelEventNotification;
use Carbon\Carbon;
use DB;
use Log;

class Event extends BaseModel
{
  public static $cacheKey = 'events';

    protected $fillable = [
        'name',
        'description',
        'max_num_participants',
        'date',
        'start_time',
        'end_time',
        'image_id',
        'category_id',
        'owner_id',
        'location_id',
        'statusName',
    ];

    protected $with = [
        'image',
        'owner',
        'location'
    ];

    protected $hidden = [
        // 'category'
    ];

    protected $appends = [
        'categoryName',
        'registeredNumber',
        'registeredIds',
        'duration'
    ];

    public function getCategoryNameAttribute()
    {
        $categoryName = $this->category->name;
        return $categoryName;
    }
    public function getDurationAttribute()
    {
      try {
          $startTime = Carbon::createFromFormat('H:i:s', $this->start_time);
          $endTime = Carbon::createFromFormat('H:i:s', $this->end_time);

          // Check if both times are valid
        if ($startTime && $endTime) {
            $durationInSeconds = $endTime->diffInSeconds($startTime);
            $formattedDuration = gmdate('H:i:s', $durationInSeconds);
            return $formattedDuration;
        } else {
            return 'Invalid time format';
        }
      } catch (\Exception $e) {
          // Handle any exceptions that might occur
          return 'Error: ' . $e->getMessage();
      }
    }

    public function getRegisteredNumberAttribute()
    {
        $registered = $this->registereds()->get()->count();
        return $registered;
    }
    public function getRegisteredIdsAttribute()
    {
        $registered = $this->registereds()->select('users.id')->pluck('id')->toArray();
        return $registered;
    }


    protected static function booted()
    {
        parent::booted();
        static::created(function ($event) {
            $user = $event->owner;
            $user->givePermission('events.' . $event->id . '.read');
            $user->givePermission('events.' . $event->id . '.update');
            // $user->givePermission('events.' . $event->id . '.delete');
            $user->givePermission('events.' . $event->id . '.cancel');
        });
        static::deleted(function ($event) {
            $permissions = Permission::where('name', 'like', 'events.' . $event->id . '.%')->get();
            DB::table('users_permissions')->whereIn('permission_id', $permissions->pluck('id'))->delete();
            Permission::destroy($permissions->pluck('id'));
        });
    }


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function location()
    {
        return $this->belongsTo(Location::class);
    }



    public function owner()
    {
        return $this->belongsTo(User::class);
    }
    public function image()
    {
        return $this->belongsTo(Upload::class);
    }

    public function registereds()
    {
        return $this->belongsToMany(User::class, 'events_users');
    }
    public function syncUsers($userIds)
    {
        $users = User::whereIn('id', $userIds)->get();
        $this->registereds()->sync($users);
    }
    public function detachUsers($userIds)
    {
        $users = User::whereIn('id', $userIds)->get();
        $this->registereds()->detach($users);
    }

    public function rules($id = null)
    {
        $id = $id ?? request()->route('id');
        $rules = [
            'name' => 'required|unique:events,name',
            'description' => 'required',
            'max_num_participants' => 'required|numeric|integer|min:1',
            'date' => 'required|date',
            'start_time' => 'required|string',
            'statusName' => 'nullable|string',
            'end_time' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'owner_id' => 'required|exists:users,id',
            'image_id' => 'required|exists:uploads,id',
            'location_id' => 'required|exists:locations,id',
        ];
        if ($id !== null) {
            $rules['name'] .= ',' . $id;
        }
        return $rules;
    }
}
