<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\NectaScraperService;


class NectaApiController extends Controller
{
    protected $scraper;

    public function __construct(NectaScraperService $scraper)
    {
        $this->scraper = $scraper;
    }

    public function getResults(Request $request)
    {
        $url = $request->get('url'); // Expecting URL as a request parameter

        if (!$url) {
            return response()->json(['error' => 'URL is required'], 400);
        }

        try {
            $results = $this->scraper->scrapeResults($url);
            return response()->json($results);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to scrape data', 'details' => $e->getMessage()], 500);
        }
    }
}
