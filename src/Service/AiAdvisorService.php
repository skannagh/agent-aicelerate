<?php

namespace App\Service;
use App\Llm\LlmClientInterface;

class AiAdvisorService
{
    public function __construct(private LlmClientInterface $llmClient)
    {
        // Initialize any AI-related services or configurations here
    }

    public function explain(array $metrics): string
    {
        $prompt = <<<PROMPT
        You are a web performance expert.
        The page has these metrics:
        JSON: {$this->jsonEncode($metrics)}

        Explain why the page is slow in simple terms,
        prioritize fixes by impact, and suggest actionable steps.
        Provide recommendations that a developer can implement.
        Provide Business Impact where possible.
        Give examples.
        Give the response in markdown format.
        Analyze these metrics and give performance percentages for each category (e.g. Performance, Accuracy, Speed, Reliability, Scalability, Overall Efficiency, Accessibility, SEO, Best Practices) based on the provided metrics and generate a graph.
        PROMPT;

        return $this->llmClient->ask($prompt);
    }

    public function generateGraphMetrics(array $metrics): string
    {
        // This is a placeholder for graph generation logic.
        // You can use libraries like Chart.js, Google Charts, or any other graphing library to create a visual representation of the metrics.
        // For example, you could generate a bar chart showing the performance percentages for each category.
        $prompt = <<<EOT
        Analyze the following metrics and return JSON only:
        Metrics: {$this->jsonEncode($metrics)}
        Format:
        [
            {"label": "Accuracy", "value": 92},
            {"label": "Speed", "value": 85},
            {"label": "Reliability", "value": 78},
            {"label": "Scalability", "value": 88},
            {"label": "Efficiency", "value": 86}
        ]
        EOT;
    
        return $this->llmClient->ask($prompt);
    }

    private function jsonEncode(array $data): string
    {
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}