<?php

namespace App;

use App\Traits\Favouriteable;
use App\Traits\RecordsActivity;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use Favouriteable, RecordsActivity;
    
    protected $guarded = [];

    protected $with = ['user', 'favourites'];

    public function getFavouritesCountAttribute()
    {
        return $this->favourites->count();
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
