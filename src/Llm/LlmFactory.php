<?php

namespace App\Llm;

class LlmFactory
{
    public function __construct(
        private OpenAiClient $openAi,
        private string $provider = 'openai',
    ) {}

    public function create(): LlmClientInterface
    {
        return $this->provider === 'openai' ? $this->openAi : throw new \InvalidArgumentException("Unsupported LLM provider: {$this->provider}");
    }
}