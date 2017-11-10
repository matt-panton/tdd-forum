<?php 

namespace App\Traits;

use App\Favourite;

trait Favouriteable
{
    protected static function bootFavouriteable()
    {
        static::deleting(function ($model) {
            $model->favourites->each->delete();
        });
    }

    public function getIsFavouritedAttribute()
    {
        return $this->isFavourited();
    }

    public function favourite($user = null)
    {
        $user = $user ?: auth()->user();
        
        if (!$this->favourites()->where('user_id', $user->id)->exists()) {
            return $this->favourites()->create(['user_id' => $user->id]);
        }
    }

    public function unfavourite($user = null)
    {
        $user = $user ?: auth()->user();
        
        $this->favourites()->where('user_id', $user->id)->get()->each->delete();
    }

    public function favourites()
    {
        return $this->morphMany(Favourite::class, 'favouriteable');
    }

    public function isFavourited()
    {
        if (!auth()->check()) {
            return false;
        }

        return auth()->user()->hasFavourited($this);
    }
}
