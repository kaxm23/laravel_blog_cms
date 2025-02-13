<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function dashboard()
    {
        $data = [
            'pageViews' => $this->analyticsService->getPageViews(),
            'topPosts' => $this->analyticsService->getTopPosts(),
            'userEngagement' => $this->analyticsService->getUserEngagement(),
            'trafficSources' => $this->analyticsService->getTrafficSources(),
            'conversionRates' => $this->analyticsService->getConversionRates(),
        ];

        return view('analytics.dashboard', $data);
    }
}