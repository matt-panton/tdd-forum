<?php

namespace App;

use App\Traits\Favouriteable;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favouriteable, RecordsActivity;
    
    protected $guarded = [];

    protected $appends = ['favourites_count', 'is_favourited'];

    protected $with = ['user', 'favourites'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function ($reply) {
            $reply->thread->decrement('replies_count');
        });
    }

    public function getFavouritesCountAttribute()
    {
        return $this->favourites->count();
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
