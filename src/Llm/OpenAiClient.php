<?php

namespace App\Llm;
ini_set('max_execution_time', '300'); // 300 seconds = 5 minutes

class OpenAiClient implements LlmClientInterface
{
    private string $apiKey;
    private string $endpoint = 'https://api.openai.com/v1/responses';

    public function __construct(string $apiKey = '')
    {
        $this->apiKey = $apiKey;
    }

    public function ask(string $prompt): string
    {
        $headers = [
            "Authorization: Bearer {$this->apiKey}",
            "Content-Type: application/json",
        ];

        $payload = [
            'model' => 'gpt-5-nano',
            'input' => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ];

        /*echo $this->endpoint;
        echo $this->apiKey;
        echo $prompt; die;*/

        $ch = curl_init($this->endpoint);
        //print_r($ch); die;
        curl_setopt_array($ch, [
            CURLOPT_URL => $this->endpoint,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
        ]);

        $response = curl_exec($ch);
        if(curl_errno($ch)){
            echo "OpenAI Call Error: " . curl_error($ch);
        }

        $data = json_decode($response, true);
        // to display the entire response for debugging
        /*echo "<pre>";
        print_r($data); die;*/
        return $data['output'][1]['content'][0]['text'] ?? 'No response from OpenAI API';
    }
}