<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Userfilter extends Model
{
    protected $fillable = ['user_id','filter'];
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
