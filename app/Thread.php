<?php

namespace App;

use Laravel\Scout\Searchable;
use App\Traits\RecordsActivity;
use App\Events\ThreadReceivedNewReply;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity, Searchable;

    protected $guarded = [];

    protected $with = ['channel'];

    protected $casts = [
        'locked' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('withUser', function ($builder) {
            $builder->with('user');
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function toSearchableArray()
    {
        $data = $this->toArray();

        $data['path'] = $this->path();

        return $data;
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
        $reply = $this->replies()->create($data);

        event(new ThreadReceivedNewReply($reply));

        return $reply;
    }

    public function subscribe($user = null)
    {
        $user = $user ?: auth()->user();

        $this->subscriptions()->create([
            'user_id' => $user->id,
        ]);

        return $this;
    }

    public function unsubscribe($user = null)
    {
        $user = $user ?: auth()->user();

        $this->subscriptions()->where([
            'user_id' => $user->id,
        ])->delete();

        return $this;
    }

    public function userIsSubscribed(User $user)
    {
        return $this->subscriptions()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function markBestReply($reply = null)
    {
        $this->update([
            'best_reply_id' => $reply ? $reply->id : null
        ]);

        return $this;
    }

    public function hasUpdatesFor($user = null)
    {
        $user = $user ?: auth()->user();

        if (is_null($user)) return false;

        return $this->updated_at > cache($user->visitedThreadCacheKey($this));
    }

    public function getIsSubscribedToAttribute()
    {
        return auth()->guest()
            ? false :
            $this->userIsSubscribed(auth()->user());
    }

    public function setSlugAttribute($value)
    {
        $slug = str_slug($value);

        if (static::whereSlug($slug)->exists()) {
            $slug = sprintf('%s-%s', $slug, $this->id);
        }

        $this->attributes['slug'] = $slug;
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
