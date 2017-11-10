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

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }

    public function path()
    {
        return route('thread.show', [$this->channel, $this]);
    }

    public function addReply(array $data)
    {
        return $this->replies()->create($data);
    }

    public function subscribe($user = null)
    {
        $user = $user ?: auth()->user();

        $this->subscriptions()->create([
            'user_id' => $user->id,
        ]);
    }

    public function unsubscribe($user = null)
    {
        $user = $user ?: auth()->user();

        $this->subscriptions()->where([
            'user_id' => $user->id,
        ])->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
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
