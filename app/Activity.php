<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $guarded = [];

    public static function feed(User $user, $count = 50)
    {
        return static::where('user_id', $user->id)
            ->with('subject')
            ->latest()
            ->take($count)
            ->get()
            ->groupBy(function($activity) {
                return $activity->created_at->format('d-m-Y');
            });
    }

    public function subject()
    {
        return $this->morphTo();
    }
}
