<?php 

namespace App\Filters;

use App\User;

class ThreadFilters extends Filters
{
    protected $filters = [
        'by',
        'popular',
        'unanswered',
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

    /**
     * Filter threads by only those that have no replies.
     * 
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}
