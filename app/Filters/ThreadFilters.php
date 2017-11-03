<?php 

namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    protected $filters = [
        'by',
    ];

    /**
     * Filter the query by a given username.
     *  
     * @param  string  $username
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function by($username)
    {
        if (! $user = User::where('name', $username)->first()) {
            return $this->builder;
        }

        return $this->builder->where('user_id', $user->id);
    }
}
