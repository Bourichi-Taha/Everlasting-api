<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends BaseModel
{
  public static $cacheKey = 'statuses';
    protected $fillable = [
        'name',
    ];
    public function events()
    {
        return $this->hasMany(Event::class);
    }
    public function rules($id = null)
    {
        $id = $id ?? request()->route('id');
        $rules = [
            'name' => 'required|unique:statuses,name',
        ];
        if ($id !== null) {
            $rules['name'] .= ',' . $id;
        }
        return $rules;
    }
}
