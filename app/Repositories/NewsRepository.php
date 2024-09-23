<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Redis;

class NewsRepository
{

    public function saveNews(array $newsData): void
    {
        Redis::lpush('crypto_news', json_encode($newsData));
    }

    public function getNews($limit = -1)
    {
        return Redis::lrange('crypto_news', 0, $limit);
    }


}
