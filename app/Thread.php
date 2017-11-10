<?php

namespace App;

use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $with = ['channel'];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withUser', function ($builder) {
            $builder->with('user');
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });
    }

    public function path()
    {
        return route('thread.show', [$this->channel, $this]);
    }

    public function addReply(array $data)
    {
        return $this->replies()->create($data);
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }
}
