<?php

namespace App\Jobs;

use App\Services\NewsService;

class FetchCryptoNews
{
    protected $newsService;

    public function __construct()
    {
        $this->newsService = app(NewsService::class);
    }

    public function handle()
    {
        $this->newsService->fetchAndSaveNews();
    }
}
