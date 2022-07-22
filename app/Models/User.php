<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function accounts()
    {
        return $this->belongsToMany(Account::class)->withPivot('notify');
    }

    public function userfilter()
    {
        return $this->belongsTo(Userfilter::class);
    }

    static function boot()
    {
        self::saving(function ($model){
            if ($model->isDirty('open_pass')) {
                $model->password = Hash::make($model->open_pass);
            }
        });
        self::created(function ($model){
            $filter = new Userfilter();
            $filter->user_id = $model->id;
            $filter->filter = '{"status":[2,3,1,4,5],"soc_net":[],"period":{"id":2,"title":"\u0422\u0435\u043a\u0443\u0449\u0438\u0439 \u043c\u0435\u0441\u044f\u0446"},"tags":[],"public_to":"01\/11\/2021","public_from":"01\/10\/2021","account_id":"14"}';
            $filter->save();
        });
        parent::boot(); // TODO: Change the autogenerated stub
    }
    public function scopeWithAccounts($query, $account_id)
    {
        $query->whereHas('accounts', function ($q) use ($account_id) {
            $q->where('id', $account_id);
        });
    }
}
