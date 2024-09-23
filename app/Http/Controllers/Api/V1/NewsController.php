<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsService $newsService)
    {
        $this->newsService = $newsService;
    }

    public function searchBySymbol(Request $request)
    {
        $symbol = $request->input('symbol');

        $filteredNews = $this->newsService->filterNewsBySymbol($symbol);

        return response()->json(array_values($filteredNews));
    }

    public function searchByTime(Request $request)
    {
        $symbol = $request->input('symbol');
        $fromDate = strtotime($request->input('fromDate'));
        $toDate = strtotime($request->input('toDate', now()));

        $filteredNews = $this->newsService->filterNewsByTime($symbol, $fromDate, $toDate);

        return response()->json(array_values($filteredNews));
    }
}
