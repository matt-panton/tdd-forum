<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    protected $guarded = [];

    protected $with = ['channel'];

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
