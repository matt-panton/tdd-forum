<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password', 'remember_token', 'email',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    /**
     * Determine whether user has favourited given model.
     * 
     * @param  Illuminate\Database\Eloquent\Model  $model [description]
     * @return boolean
     */
    public function hasFavourited(Model $model)
    {
        if (!method_exists($model, 'favourites')) {
            return false;
        }

        return $model->favourites->where('user_id', $this->id)->isNotEmpty();
    }

    /**
     * Determine whether user owns a given model.
     * 
     * @param  Illuminate\Database\Eloquent\Model  $model [description]
     * @return boolean
     */
    public function owns(Model $model)
    {
        return (int)$model->user_id === (int)$this->id;
    }

    public function threads() 
    {
        return $this->hasMany(Thread::class)->latest();
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }
}
