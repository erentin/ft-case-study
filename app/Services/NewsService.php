<?php

namespace App\Services;

use App\Repositories\NewsRepository;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class NewsService
{
    protected $client;
    protected $newsRepository;

    public function __construct(NewsRepository $newsRepository, Client $client)
    {
        $this->newsRepository = $newsRepository;
        $this->client = $client;
    }

    public function fetchAndSaveNews(): void
    {
        
        $url = config('services.cryptopanic.base_url') . '/posts/?auth_token=' . config('services.cryptopanic.api_key') . '&public=true';

        try {
            Log::info('API çağrısı yapılıyor: ' . $url); 
            $response = $this->client->get($url);
            $data = json_decode($response->getBody(), true);

            foreach ($data['results'] as $news) {
                $title = $news['title'];
                $time = $news['published_at'];
                $symbols = isset($news['currencies']) ? implode(',', array_column($news['currencies'], 'code')) : '';

                // Haber verilerini bir araya getir
                $newsData = [
                    'title' => $title,
                    'time' => $time,
                    'symbols' => $symbols,
                ];

                // Log işlemi ve Redis'e kaydetme
                Log::info("Redis'e kaydediliyor: {$title}, {$time}, {$symbols}");
                $this->newsRepository->saveNews($newsData);
            }

        } catch (\Exception $e) {
            // Eğer hata olursa log'la
            Log::error('CryptoPanic API verileri çekilirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    public function filterNewsBySymbol($symbol)
    {
        $news = $this->newsRepository->getNews(20);

        return array_filter(array_map(function ($item) use ($symbol) {
            $decoded = json_decode($item, true);

            return str_contains($decoded['symbols'], $symbol) ? $decoded : null;
        }, $news));
    }

    public function filterNewsByTime($symbol, $fromDate, $toDate)
    {
        $news = $this->newsRepository->getNews();

        return array_filter(array_map(function ($item) use ($symbol, $fromDate, $toDate) {
            $decoded = json_decode($item, true);
            $time = strtotime($decoded['time']);

            if (!empty($fromDate) && $time < $fromDate) {
                return null;
            }

            if ($time > $toDate) {
                return null;
            }

            if (!empty($symbol) && !str_contains($decoded['symbols'], $symbol)) {
                return null;
            }

            return $decoded;
        }, $news));
    }
}
