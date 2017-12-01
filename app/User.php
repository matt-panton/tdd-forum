<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'avatar_path', 'confirmed', 'confirmation_token'
    ];

    protected $appends = ['avatar'];

    protected $hidden = [
        'password', 'remember_token', 'email',
    ];

    protected $casts = [
        'confirmed' => 'boolean'
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }

    public function visitedThreadCacheKey(Thread $thread)
    {
        return sprintf("users.%s.visits.%s", $this->id, $thread->id);
    }

    public function read(Thread $thread)
    {
        cache()->forever(
            $this->visitedThreadCacheKey($thread),
            Carbon::now()
        );
    }

    public function isAdmin()
    {
        return in_array($this->name, ['matt_panton']);
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

    public function confirm()
    {
        $this->confirmed = true;
        $this->confirmation_token = null;

        $this->save();

        return $this;
    }

    public function removeExistingAvatar()
    {
        if (is_null($this->avatar_path)) {
            return $this;
        }

        Storage::disk('public')->delete($this->avatar_path);
        
        $this->update(['avatar_path' => null]);

        return $this;
    }

    public function getAvatarAttribute()
    {
        return $this->avatar_path
            ? asset("storage/{$this->avatar_path}")
            : asset("images/avatar-default.png");
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
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
