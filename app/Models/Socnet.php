<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Socnet extends Model
{
    public function accounts()
    {
        return $this->belongsToMany(Account::class);
    }
}
