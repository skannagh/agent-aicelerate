<?php

namespace App\Controller;

use App\Service\AiAdvisorService;
use App\Service\MetricsCollectorService;
use App\Service\PageSpeedService;
use App\Service\PerformanceAggregatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\GeminiService;

class PageSpeedController extends AbstractController
{
 
    #[Route('/aicheck', name: 'app_home')]
    public function index(): Response
    {
        //print_r(phpinfo()); die;

        // Option 1: Return plain text
        return new Response('Hello Symfony, my first AI Agent!');

        // Option 2: Render a Twig template
        /*return $this->render('ai/page_speed_dashboard.html.twig', [
            'controller_name' => 'HomeController',
        ]);*/
    }
    
    #[Route('/ai/page-speed-analyze', name: 'ai_page_speed_analyze')]
    public function analyze(Request $request, MetricsCollectorService $collector, AiAdvisorService $aiAdvisor, PageSpeedService $pageSpeedService): Response
    {
        $url = $request->query->get('url');
        $pageMetrics = $collector->collect($url);
        $pageSpeedMetrics = $pageSpeedService->analyze($url);
        $metrics = array_merge($pageMetrics, $pageSpeedMetrics);
        /*echo "<pre>";
        print_r($metrics); die;*/
        $aiAnalysis = $aiAdvisor->explain($metrics);
        $aiPerformanceGraphMetris = $aiAdvisor->generateGraphMetrics($metrics);
        $perfMetricsJson = json_decode($aiPerformanceGraphMetris, true);
        $perfMetricsJsonChecked = is_array($perfMetricsJson) ? $perfMetricsJson : [];

        /*echo "<pre>";
        print_r($perfMetricsJsonChecked); die;*/

        return $this->render('ai/page_speed_dashboard.html.twig', [
            'url' => $url,
            'metrics' => $metrics,
            'ai_analysis' => $aiAnalysis,
            'ai_performance_graph_metrics' => $perfMetricsJsonChecked,
        ]);
    }
}