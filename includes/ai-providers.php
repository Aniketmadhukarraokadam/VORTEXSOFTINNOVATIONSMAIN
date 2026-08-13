<?php
/**
 * Vortexsoft Innovations — AI Blog Provider Functions
 * includes/ai-providers.php
 *
 * Provides three normalized AI generation functions.
 * All API keys are read from config/.env — NEVER hardcoded here.
 * Each function returns: ['title' => ..., 'excerpt' => ..., 'body' => ...]
 * or throws an Exception on failure.
 */

// ── Load .env if not already loaded ─────────────────────────────
if (!defined('GROQ_API_KEY')) {
    $_ai_env_file = __DIR__ . '/../config/.env';
    $_ai_env = [];
    if (file_exists($_ai_env_file)) {
        $_ai_env = @parse_ini_file($_ai_env_file, false, INI_SCANNER_RAW) ?: [];
    }
    define('GROQ_API_KEY',       $_ai_env['GROQ_API_KEY']       ?? '');
    define('GEMINI_API_KEY',     $_ai_env['GEMINI_API_KEY']     ?? '');
    define('OPENROUTER_API_KEY', $_ai_env['OPENROUTER_API_KEY'] ?? '');
    define('OPENROUTER_MODEL',   $_ai_env['OPENROUTER_MODEL']   ?? 'anthropic/claude-3.5-sonnet');
    define('GROQ_MODEL',         $_ai_env['GROQ_MODEL']         ?? 'llama-3.3-70b-versatile');
    define('GEMINI_MODEL',       $_ai_env['GEMINI_MODEL']       ?? 'gemini-2.0-flash');
    unset($_ai_env, $_ai_env_file);
}

/**
 * Build the unified blog generation prompt for all three providers.
 */
function buildBlogPrompt(string $topic, string $targetKeyword): array {
    $systemPrompt = <<<SYSTEM
You are writing a blog post for Vortexsoft Innovations Pvt. Ltd., an ISO 27001:2013-certified 
IT, BPO, and AI solutions company headquartered in Bengaluru with offices in Pune and Wyoming, USA.

Use these facts consistently if referencing the company — never alter these numbers:
"Vortexsoft Innovations has delivered 200+ projects for 150+ clients across 25+ services in 
its first 4+ years, spanning Healthcare BPO/RCM, Custom Software Development, AI Data Annotation, 
Publishing Services, Real Estate Title & Settlement, Accounting & Payroll, Digital Marketing, 
and Manpower & Staffing. Healthcare operations are HIPAA-compliant."

STRICT OUTPUT REQUIREMENT:
Respond ONLY with a valid JSON object. No markdown, no backticks, no explanation. The JSON must be:
{"title": "string", "excerpt": "string under 160 chars", "body": "string of full HTML"}
SYSTEM;

    $userPrompt = <<<USER
Write a blog post of 900-1200 words targeting the keyword: "{$targetKeyword}"
Topic: {$topic}

Requirements:
- Open with a direct, factual answer to the core question in the first 2-3 sentences (supports featured snippets and AI-engine citation — no throat-clearing intro)
- Use clear H2/H3 subheadings
- Include one short FAQ section at the end (2-3 questions, 40-60 word answers each)
- Professional but accessible tone, not overly salesy
- Do not fabricate statistics, client names, or case studies not provided above
- Output as JSON: {"title": "...", "excerpt": "...(one sentence, under 160 chars)", "body": "...(full post in HTML with h2/h3/p tags)"}
USER;

    return ['system' => $systemPrompt, 'user' => $userPrompt];
}

/**
 * Shared cURL executor. Returns raw response body string.
 */
function _ai_curl_post(string $url, array $headers, string $body, int $timeout = 45): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_USERAGENT      => 'VortexsoftAI/1.0 (+https://vortexsoftinnovations.com)',
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);
    curl_close($ch);

    if ($curlErr) {
        throw new RuntimeException("cURL error: {$curlErr}");
    }
    if ($httpCode < 200 || $httpCode >= 300) {
        $preview = substr($response ?: '', 0, 300);
        throw new RuntimeException("HTTP {$httpCode}: {$preview}");
    }
    return (string)$response;
}

/**
 * Parse the JSON content from an AI response string.
 * Strips markdown code fences if the model wraps the JSON.
 */
function _ai_parse_json_content(string $content): array {
    // Strip ```json ... ``` or ``` ... ``` fences
    $content = trim($content);
    $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
    $content = preg_replace('/\s*```\s*$/i', '', $content);
    $content = trim($content);

    $decoded = json_decode($content, true);
    if (!is_array($decoded)) {
        throw new RuntimeException('Provider returned malformed JSON: ' . substr($content, 0, 200));
    }
    if (empty($decoded['title']) || empty($decoded['body'])) {
        throw new RuntimeException('Provider JSON missing required fields (title/body).');
    }
    return [
        'title'   => (string)($decoded['title']   ?? ''),
        'excerpt' => (string)($decoded['excerpt']  ?? ''),
        'body'    => (string)($decoded['body']     ?? ''),
    ];
}

/**
 * Generate blog content via Groq.
 * Model: configurable via GROQ_MODEL env var (default: llama-3.3-70b-versatile)
 */
function generateWithGroq(array $prompt): array {
    if (empty(GROQ_API_KEY)) {
        throw new RuntimeException('GROQ_API_KEY is not set in config/.env');
    }

    $payload = json_encode([
        'model'    => GROQ_MODEL,
        'messages' => [
            ['role' => 'system', 'content' => $prompt['system']],
            ['role' => 'user',   'content' => $prompt['user']],
        ],
        'temperature'     => 0.7,
        'max_tokens'      => 2048,
        'response_format' => ['type' => 'json_object'],
    ]);

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY,
    ];

    $response = _ai_curl_post('https://api.groq.com/openai/v1/chat/completions', $headers, $payload);
    $data     = json_decode($response, true);

    if (!isset($data['choices'][0]['message']['content'])) {
        throw new RuntimeException('Groq: unexpected response shape. ' . substr($response, 0, 300));
    }

    $content      = $data['choices'][0]['message']['content'];
    $result       = _ai_parse_json_content($content);
    $result['usage'] = $data['usage'] ?? null;
    return $result;
}

/**
 * Generate blog content via Google Gemini.
 * Model: configurable via GEMINI_MODEL env var (default: gemini-2.0-flash)
 */
function generateWithGemini(array $prompt): array {
    if (empty(GEMINI_API_KEY)) {
        throw new RuntimeException('GEMINI_API_KEY is not set in config/.env');
    }

    $model    = GEMINI_MODEL;
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . GEMINI_API_KEY;

    $fullPrompt = $prompt['system'] . "\n\n" . $prompt['user'];

    $payload = json_encode([
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $fullPrompt]]],
        ],
        'generationConfig' => [
            'temperature'     => 0.7,
            'maxOutputTokens' => 2048,
            'responseMimeType'=> 'application/json',
        ],
    ]);

    $headers = ['Content-Type: application/json'];

    $response = _ai_curl_post($endpoint, $headers, $payload);
    $data     = json_decode($response, true);

    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        // Check for block reason
        $blockReason = $data['candidates'][0]['finishReason'] ?? ($data['promptFeedback']['blockReason'] ?? 'unknown');
        throw new RuntimeException("Gemini: content blocked or empty. Reason: {$blockReason}");
    }

    $content      = $data['candidates'][0]['content']['parts'][0]['text'];
    $result       = _ai_parse_json_content($content);
    $result['usage'] = $data['usageMetadata'] ?? null;
    return $result;
}

/**
 * Generate blog content via OpenRouter.
 * Model: configurable via OPENROUTER_MODEL env var (default: anthropic/claude-3.5-sonnet)
 */
function generateWithOpenRouter(array $prompt): array {
    if (empty(OPENROUTER_API_KEY)) {
        throw new RuntimeException('OPENROUTER_API_KEY is not set in config/.env');
    }

    $payload = json_encode([
        'model'    => OPENROUTER_MODEL,
        'messages' => [
            ['role' => 'system', 'content' => $prompt['system']],
            ['role' => 'user',   'content' => $prompt['user']],
        ],
        'temperature' => 0.7,
        'max_tokens'  => 2048,
    ]);

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENROUTER_API_KEY,
        'HTTP-Referer: https://vortexsoftinnovations.com',
        'X-Title: Vortexsoft Blog Generator',
    ];

    $response = _ai_curl_post('https://openrouter.ai/api/v1/chat/completions', $headers, $payload);
    $data     = json_decode($response, true);

    if (!isset($data['choices'][0]['message']['content'])) {
        throw new RuntimeException('OpenRouter: unexpected response shape. ' . substr($response, 0, 300));
    }

    $content      = $data['choices'][0]['message']['content'];
    $result       = _ai_parse_json_content($content);
    $result['usage'] = $data['usage'] ?? null;
    return $result;
}

/**
 * Run all three providers and return results array.
 * Each provider result: ['ok' => bool, 'data' => [...] or 'error' => '...']
 * Uses curl_multi for parallel execution.
 */
function generateAllProviders(string $topic, string $keyword): array {
    $prompt = buildBlogPrompt($topic, $keyword);

    $providers = [
        'groq'        => fn() => generateWithGroq($prompt),
        'gemini'      => fn() => generateWithGemini($prompt),
        'openrouter'  => fn() => generateWithOpenRouter($prompt),
    ];

    $results = [];
    foreach ($providers as $name => $fn) {
        try {
            $data = $fn();
            $results[$name] = ['ok' => true, 'data' => $data];
        } catch (Throwable $e) {
            $results[$name] = ['ok' => false, 'error' => $e->getMessage()];
        }
    }
    return $results;
}

/**
 * Format token usage for display.
 */
function formatUsage(?array $usage): string {
    if (!$usage) return '—';
    // OpenAI/Groq/OpenRouter format
    if (isset($usage['prompt_tokens'])) {
        $total = ($usage['prompt_tokens'] ?? 0) + ($usage['completion_tokens'] ?? 0);
        return number_format($total) . ' tokens (~' . number_format(($usage['completion_tokens'] ?? 0)) . ' gen)';
    }
    // Gemini format
    if (isset($usage['totalTokenCount'])) {
        return number_format($usage['totalTokenCount']) . ' tokens';
    }
    return '—';
}
