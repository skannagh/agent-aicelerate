**Project**

AIcelerate — an experimental Symfony app that analyzes web page performance using lightweight metrics collection, the Google PageSpeed Insights API, and an LLM to produce human-friendly recommendations and charts.

**Requirements**

- PHP 8.4+
- Composer
- A web server (Symfony CLI recommended) or PHP built-in server

**Setup**

- Install dependencies: `composer install`
- Copy environment file and set secrets: `cp .env .env.local` (or create `.env.local`).

**Configuration**

- The app reads keys from environment variables configured in [config/services.yaml](config/services.yaml):
  - `OPENAI_API_KEY` — API key for the LLM provider
  - `PAGESPEED_API_KEY` — Google PageSpeed Insights API key
  - `LLM_PROVIDER` — optional provider string used by the LLM factory
- On Windows you can create `.env.local` with entries like:

```
OPENAI_API_KEY="sk-..."
PAGESPEED_API_KEY="AIza..."
LLM_PROVIDER="openai"
```

**Run (development)**

- Start the app (recommended): `symfony serve` or use PHP built-in server: `php -S localhost:8000 -t public`

**Usage**

- Open a browser and call the analyze route with a `url` query parameter:

```
GET /ai/page-speed-analyze?url=https://example.com
```

- This renders a dashboard at `templates/ai/page_speed_dashboard.html.twig` showing collected metrics, PageSpeed results, an AI-generated analysis, and charts.

**Key Files**

- [src/Controller/PageSpeedController.php](src/Controller/PageSpeedController.php) — controller exposing the analyze route and rendering the dashboard.
- [src/Service/MetricsCollectorService.php](src/Service/MetricsCollectorService.php) — collects simple page metrics and loading timings.
- [src/Service/PageSpeedService.php](src/Service/PageSpeedService.php) — calls Google PageSpeed Insights API.
- [src/Service/AiAdvisorService.php](src/Service/AiAdvisorService.php) — builds prompts and asks the LLM for recommendations.
- [src/Llm/OpenAiClient.php](src/Llm/OpenAiClient.php) — lightweight OpenAI-compatible client used by default.

**How It Works**

- `MetricsCollectorService` fetches page HTML and extracts size, script/image counts, and uses cURL to measure network timings.
- `PageSpeedService` queries Google PageSpeed Insights and returns core Lighthouse metrics (FCP, LCP, CLS, TTI, TBT, INP, performance score, etc.).
- `AiAdvisorService` formats a prompt containing the combined metrics and sends it to the configured LLM via the `LlmClientInterface`. The LLM response is displayed on the dashboard and used to generate chart data.

**Security & Notes**

- The repository currently contains example API keys in source files; DO NOT commit real secrets. Use environment variables as shown above.
- `src/Llm/OpenAiClient.php` and `src/Service/PageSpeedService.php` contain hard-coded default keys — override them via env vars and remove hard-coded values for production.
# Hard-coded defaults have been removed from source. Use `.env.local` or the provided `.env.example` instead.
# See `.env.example` in the project root for placeholders.
- The app performs outgoing HTTP requests to target sites; be careful when analyzing sites you don't control.

**Development**

- To change the LLM provider, implement `App\\Llm\\LlmClientInterface` and register it in [config/services.yaml](config/services.yaml).
- Frontend charts use Chart.js via CDN in the Twig template; you can swap to local assets if desired.

**Troubleshooting**

- If you see empty AI responses, ensure `OPENAI_API_KEY` is set and valid, and check network connectivity.
- If PageSpeed calls fail, verify `PAGESPEED_API_KEY` and quota limits for the API.

If you'd like, I can also:
- Add a `.env.example` file with placeholders.
- Remove the hard-coded keys and wire all secrets to env vars only.
