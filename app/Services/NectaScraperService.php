<?php

namespace App\Services;

use GuzzleHttp\Client;

class NectaScraperService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function scrapeResults($url)
    {
        $crawler = $this->client->request('GET', $url);

        // Extract data
        $data = [
            'examination_centre_ranking' => $crawler->filter('td:contains("EXAMINATION CENTRE RANKING")')->nextAll()->text(),
            'examination_centre_region' => $crawler->filter('td:contains("EXAMINATION CENTRE REGION")')->nextAll()->text(),
            'total_passed_candidates' => $crawler->filter('td:contains("TOTAL PASSED CANDIDATES")')->nextAll()->text(),
            'examination_centre_gpa' => $crawler->filter('td:contains("EXAMINATION CENTRE GPA")')->nextAll()->text(),
            'centre_category' => $crawler->filter('td:contains("CENTRE CATEGORY")')->nextAll()->text(),
            'centre_position_region' => $crawler->filter('td:contains("CENTRE POSITION IN ITS CATEGORY (REGIONWIDE)")')->nextAll()->text(),
            'centre_position_nation' => $crawler->filter('td:contains("CENTRE POSITION IN ITS CATEGORY (NATIONWIDE)")')->nextAll()->text(),
            'examination_centre_division_performance' => [
                'regist' => $crawler->filter('td:contains("REGIST")')->nextAll()->text(),
                'absent' => $crawler->filter('td:contains("ABSENT")')->nextAll()->text(),
                'sat' => $crawler->filter('td:contains("SAT")')->nextAll()->text(),
                'withheld' => $crawler->filter('td:contains("WITHHELD")')->nextAll()->text(),
                'no_ca' => $crawler->filter('td:contains("NO-CA")')->nextAll()->text(),
                'clean' => $crawler->filter('td:contains("CLEAN")')->nextAll()->text(),
                'div_i' => $crawler->filter('td:contains("DIV I")')->nextAll()->text(),
                'div_ii' => $crawler->filter('td:contains("DIV II")')->nextAll()->text(),
                'div_iii' => $crawler->filter('td:contains("DIV III")')->nextAll()->text(),
                'div_iv' => $crawler->filter('td:contains("DIV IV")')->nextAll()->text(),
                'div_0' => $crawler->filter('td:contains("DIV 0")')->nextAll()->text(),
            ],
        ];

        dd($data);

        return $data;
    }
}
