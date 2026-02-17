<?php

namespace App\Llm;

interface LlmClientInterface
{
    public function ask(string $prompt): string;
}