<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    public function socnets()
    {
        return $this->belongsToMany(Socnet::class)->withPivot('URI','online');
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('notify');
    }
    public function plans()
    {
        return $this->hasMany(Plan::class);
    }

    public function scopeWithUsers($query, $user_id)
    {
        $query->whereHas('users', function ($q) use ($user_id) {
            $q->where('id', $user_id);
        });
    }
}
