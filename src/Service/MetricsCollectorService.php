<?php

namespace App\Service;

class MetricsCollectorService
{
    public function collect(string $url): array
    {
        $opts = [
        "http" => [
        "header" => "Accept: application/json\r\n" .
                    "User-Agent: MyApp/1.0\r\n"
            ]
        ];

        $context = stream_context_create($opts);    

        $html = file_get_contents($url, false, $context);

        // Basic metrics
        $pageSizeKB = strlen($html) / 1024;
        $numScripts = substr_count($html, '<script');
        $numImages = substr_count($html, '<img');
        $loadingMetrics = $this->measureLoadingMetrics($url);

        return [
            'page_metrics' => [
                'page_size_kb' => round($pageSizeKB, 2),
                'total_scripts' => $numScripts,
                'total_images' => $numImages,
            ],
            'loading_metrics' => [
                'ttfb' => $loadingMetrics['ttfb'] ?? 0,
                'dns_time' => $loadingMetrics['dns_time'] ?? 0,
                'ssl_handshake_time' => $loadingMetrics['ssl_handshake_time'] ?? 0,
                'connection_time' => $loadingMetrics['connection_time'] ?? 0,
                'download_time' => $loadingMetrics['download_time'] ?? 0,
            ],
            'timestamp' => (new \DateTimeImmutable())->format('Y-m-d H:i:s'),
        ];
    }

    private function measureLoadingMetrics(string $url): array
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_exec($ch);

        $info = curl_getinfo($ch);
        //print_r($info); die;
        $ttfb = $info['starttransfer_time'] * 1000; // ms
        $dnsTime = $info['namelookup_time'] * 1000; // ms
        $connectionTime = $info['connect_time'] * 1000; // ms
        $sslHandshakeTime = $info['ssl_verify_result'] === 0 ? ($info['connect_time'] - $info['namelookup_time']) * 1000 : 0; // ms
        $downloadTime = ($info['total_time'] - $info['starttransfer_time']) * 1000; // ms

        return [
            'ttfb' => $ttfb,
            'dns_time' => $dnsTime,
            'connection_time' => $connectionTime,
            'ssl_handshake_time' => $sslHandshakeTime,
            'download_time' => $downloadTime,
        ];
    }
}