<?php 

namespace App\Traits;

use App\Favourite;

trait Favouriteable
{
    public function favourite($user = null)
    {
        $user = $user ?: auth()->user();
        
        if (!$this->favourites()->where('user_id', $user->id)->exists()) {
            return $this->favourites()->create(['user_id' => $user->id]);
        }
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
