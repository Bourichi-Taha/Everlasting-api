<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Upload;
use App\Notifications\CancelEventNotification;
use Illuminate\Http\Request;
use Storage;
use Str;

class LocationController extends CrudController
{
    protected $table = 'locations';
    protected $modelClass = Location::class;

    public function deleteOne($id, Request $request)
    {
        $user = $request->user();
        if (!$user->hasPermission($this->table, 'delete', $id)) {
            return response()->json([
                'success' => false,
                'errors' => [__('common.permission_denied')]
            ]);
        }

        $model = app($this->modelClass)->find($id);

        if (!$model) {
            return response()->json([
                'success' => false,
                'errors' => [__(Str::of($this->table)->replace('_', '-') . '.not_found')]
            ]);
        }
        $events = $model->events;
        foreach ($events as $event) {
            $users = $event->registereds()->get();
            foreach ($users as $user) {
                $user->notify(new CancelEventNotification($event->name, $user->username));
            }
        }
        $model->delete();

        // Delete linked uploads
        $rules = app($this->modelClass)->rules($id);
        foreach ($rules as $key => $value) {
            $isUpload = false;
            if (is_array($value)) {
                if (in_array('exists:uploads,id', $value)) {
                    $isUpload = true;
                }
            } elseif (str_contains($value, 'exists:uploads,id')) {
                $isUpload = true;
            }
            if ($isUpload) {
                $upload = Upload::find($model->$key);
                if ($upload) {
                    $path = $upload->path;
                    if ($path) {
                        Storage::disk('cloud')->delete($path);
                    }
                    $upload->delete();
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => __(Str::of($this->table)->replace('_', '-') . '.deleted')
        ]);
    }
}
