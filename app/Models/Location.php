<?php

namespace App\Models;

class Location extends BaseModel
{
  public static $cacheKey = 'locations';

    protected $fillable = [
        'country',
        'city',
        'state_province',
        'address',
        'postal_code',
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function rules($id = null)
    {
        $id = $id ?? request()->route('id');
        $rules = [
            'country' => 'required|string',
            'city' => 'required|string',
            'state_province' => 'string|nullable',
            'address' => 'required|string',
            'postal_code' => 'required|string',
        ];
        return $rules;
    }
}
