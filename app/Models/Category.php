<?php

namespace App\Models;

class Category extends BaseModel
{
  public static $cacheKey = 'categories';

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
            'name' => 'required|unique:categories,name',
        ];
        if ($id !== null) {
            $rules['name'] .= ',' . $id;
        }
        return $rules;
    }
}
