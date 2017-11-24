<?php 

namespace App\Repositories;

use App\Thread;
use Illuminate\Support\Facades\Redis;

class Trending
{
    public function get($count = 5)
    {
        return array_map(
            'json_decode', 
            Redis::zrevrange($this->cacheKey(), 0, $count-1)
        );
    }

    public function increment(Thread $thread)
    {
        Redis::zincrby($this->cacheKey(), 1, json_encode([
            'title' => $thread->title,
            'path'  => $thread->path(), 
        ]));
    }

    public function reset()
    {
        Redis::del($this->cacheKey());
    }

    protected function cacheKey()
    {
        return app()->environment('testing')
            ? 'testing_trending_threads'
            : 'trending_threads';
    }
}
