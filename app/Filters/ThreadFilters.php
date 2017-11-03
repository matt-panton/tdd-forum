<?php 

namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    protected $filters = [
        'by',
        'popular',
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

    /**
     * Order threads based on number of replies.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function popular()
    {
        $this->builder->getQuery()->orders = [];

        return $this->builder->orderBy('replies_count', 'desc');
    }
}
