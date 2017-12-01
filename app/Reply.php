<?php

namespace App;

use Carbon\Carbon;
use App\Traits\Favouriteable;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favouriteable, RecordsActivity;
    
    protected $guarded = [];

    protected $appends = [
        'favourites_count',
        'is_favourited',
        'formatted_body',
        'is_best',
    ];

    protected $with = ['user', 'favourites'];

    public static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });

        static::deleted(function ($reply) {
            $reply->thread->decrement('replies_count');
            if ($reply->is_best) {
                $reply->thread->markBestReply(null);
            }
        });
    }

    public function getFavouritesCountAttribute()
    {
        return $this->favourites->count();
    }

    public function getFormattedBodyAttribute()
    {
        return preg_replace_callback('/\@([a-zA-Z0-9\-\_]+)/', function ($matches) {
            return sprintf(
                '<a href="%s">%s</a>',
                route('user.show', $matches[1]),
                $matches[0]
            );
        }, e($this->body));
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    public function mentionedUsers()
    {
        preg_match_all('/\@([a-zA-Z0-9\-\_]+)/', $this->body, $matches);

        return $matches[1];
    }

    public function getIsBestAttribute()
    {
        return (int)$this->thread->best_reply_id === (int)$this->id;
    }

    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
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
