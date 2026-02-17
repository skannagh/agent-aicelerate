<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class PageSpeedService
{
    private string $pageSpeedApiKey;

    public function __construct(
        private HttpClientInterface $client,
        string $pageSpeedApiKey = ''
    ) {
        $this->pageSpeedApiKey = $pageSpeedApiKey;
    }

    public function analyze(string $url, string $strategy = 'desktop'): array
    {
        $response = $this->client->request('GET',
            'https://www.googleapis.com/pagespeedonline/v5/runPagespeed',
            [
                'query' => [
                    'url' => $url,
                    'key' => $this->pageSpeedApiKey,
                    'strategy' => $strategy,
                ]
            ]
        );

        $data = $response->toArray();

        return [
            'rendering_metrics' => [
                'fcp' => $data['lighthouseResult']['audits']['first-contentful-paint']['numericValue'] ?? null,
                'lcp' => $data['lighthouseResult']['audits']['largest-contentful-paint']['numericValue'] ?? null,
                'cls' => $data['lighthouseResult']['audits']['cumulative-layout-shift']['numericValue'] ?? null,
                'fmp' => $data['lighthouseResult']['audits']['first-meaningful-paint']['numericValue'] ?? null, // less used now but still available
            ],
            'interactivity_metrics' => [
                'tti' => $data['lighthouseResult']['audits']['interactive']['numericValue'] ?? null,
                'tbt' => $data['lighthouseResult']['audits']['total-blocking-time']['numericValue'] ?? null,
                //'fid' => $data['lighthouseResult']['audits']['first-input-delay']['numericValue'] ?? null, //FID is gone → replaced by INP
                'inp' => $data['lighthouseResult']['audits']['interaction-to-next-paint']['numericValue'] ?? null, //New metric replacing FID
            ],
            'performance_metrics' => [
                'performance_score' => $data['lighthouseResult']['categories']['performance']['score'] ?? null,
                'si' => $data['lighthouseResult']['audits']['speed-index']['numericValue'] ?? null,
            ],
        ];
    }
}