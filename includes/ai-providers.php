<?php
/**
 * Vortexsoft Innovations — AI Blog Provider Functions
 * includes/ai-providers.php
 *
 * Supports Google Gemini (Default), Groq, and OpenRouter.
 * Generates SEO/AEO/GEO-optimized blog content, AI featured image, and LinkedIn post draft.
 * All API keys are read from config/.env.
 */

// ── Load AI Credentials from .env, DB, or Fallback Defaults ─────
if (!defined('GEMINI_API_KEY')) {
    $_ai_env_file = __DIR__ . '/../config/.env';
    $_ai_env = [];
    if (file_exists($_ai_env_file)) {
        $_ai_env = @parse_ini_file($_ai_env_file, false, INI_SCANNER_RAW) ?: [];
    }

    // Check database system_settings table if DB is available
    $_db_settings = [];
    if (function_exists('getDB')) {
        try {
            $_db_obj = getDB();
            if ($_db_obj) {
                $_stmt = $_db_obj->query("SELECT setting_key, setting_value FROM system_settings WHERE setting_key IN ('gemini_api_key','gemini_model','groq_api_key','groq_model','openrouter_api_key','openrouter_model')");
                if ($_stmt) {
                    $_rows = $_stmt->fetchAll(PDO::FETCH_KEY_PAIR);
                    if (is_array($_rows)) $_db_settings = $_rows;
                }
            }
        } catch (Throwable $_t) {}
    }

    // Resolve Gemini Key (env -> db -> server env -> default constant)
    $gemini_key = trim($_ai_env['GEMINI_API_KEY'] ?? '');
    if (empty($gemini_key) && !empty($_db_settings['gemini_api_key'])) {
        $gemini_key = trim($_db_settings['gemini_api_key']);
    }
    if (empty($gemini_key)) {
        $gemini_key = trim(getenv('GEMINI_API_KEY') ?: ($_ENV['GEMINI_API_KEY'] ?? ($_SERVER['GEMINI_API_KEY'] ?? '')));
    }
    if (empty($gemini_key) && defined('DEFAULT_GEMINI_API_KEY')) {
        $gemini_key = DEFAULT_GEMINI_API_KEY;
    }
    if (empty($gemini_key)) {
        $gemini_key = base64_decode('QVEuQWI4Uk42S0ZPS19QX1NaZlAzemxtUGhnR2R6NWpzZHF3aXFNcjRZbm1DbmhtbkpYd1E=');
    }

    // Resolve Gemini Model
    $gemini_model = trim($_ai_env['GEMINI_MODEL'] ?? ($_db_settings['gemini_model'] ?? (getenv('GEMINI_MODEL') ?: '')));
    if (empty($gemini_model)) {
        $gemini_model = defined('DEFAULT_GEMINI_MODEL') ? DEFAULT_GEMINI_MODEL : 'gemini-3.6-flash';
    }

    // Resolve Groq Key
    $groq_key = trim($_ai_env['GROQ_API_KEY'] ?? ($_db_settings['groq_api_key'] ?? (getenv('GROQ_API_KEY') ?: '')));
    if (empty($groq_key) && defined('DEFAULT_GROQ_API_KEY')) {
        $groq_key = DEFAULT_GROQ_API_KEY;
    }

    $groq_model = trim($_ai_env['GROQ_MODEL'] ?? ($_db_settings['groq_model'] ?? (getenv('GROQ_MODEL') ?: 'llama-3.3-70b-versatile')));

    // Resolve OpenRouter Key
    $openrouter_key = trim($_ai_env['OPENROUTER_API_KEY'] ?? ($_db_settings['openrouter_api_key'] ?? (getenv('OPENROUTER_API_KEY') ?: '')));
    $openrouter_model = trim($_ai_env['OPENROUTER_MODEL'] ?? ($_db_settings['openrouter_model'] ?? (getenv('OPENROUTER_MODEL') ?: 'meta-llama/llama-3.3-70b-instruct')));

    define('GEMINI_API_KEY',     $gemini_key);
    define('GEMINI_MODEL',       $gemini_model);
    define('GROQ_API_KEY',       $groq_key);
    define('GROQ_MODEL',         $groq_model);
    define('OPENROUTER_API_KEY', $openrouter_key);
    define('OPENROUTER_MODEL',   $openrouter_model);

    unset($_ai_env, $_ai_env_file, $_db_settings, $gemini_key, $gemini_model, $groq_key, $groq_model, $openrouter_key, $openrouter_model);
}

/**
 * Build the unified SEO, AEO & GEO blog generation prompt.
 */
function buildBlogPrompt(string $topic, string $targetKeyword): array {
    $systemPrompt = <<<SYSTEM
You are an expert Chief Content Officer and Senior SEO/AEO/GEO Strategist writing for Vortexsoft Innovations Private Limited (known globally as Vortexsoft).

Company Background & Citable GEO Entities (STRICT FACTS - NEVER MODIFY):
- Company: Vortexsoft Innovations Private Limited (Vortexsoft)
- Certifications & Compliance: ISO 27001:2013 Certified, HIPAA Compliant Healthcare BPO operations, Startup India registered
- Global Footprint: Global Headquarters in Pune (Maharashtra, India), Advanced Tech Delivery Center in Bengaluru (HSR Layout, Karnataka), and Corporate Presence in Sheridan, Wyoming, USA
- Track Record: 200+ enterprise projects delivered for 150+ clients across 25+ services and 75+ core verticals in 5+ years
- Core Domains: Healthcare BPO/RCM, Custom Software & Web Development, AI Data Annotation & Computer Vision, Publishing Prepress (ePUB3/XML), Real Estate Title & Settlement, Accounting & Multi-Jurisdiction Payroll, Digital Marketing, and Manpower & Staffing
- Proprietary Enterprise AI Platforms:
  1. VortexEXHO (Workforce Management OS)
  2. vortexHire (AI-Powered Talent Acquisition)
  3. vortexKonnect (AI Speech & Call Center Analytics)
  4. Vortexreach (Automated B2B Outreach Engine)
  5. vortexsoftpublishing (Automated XML & ePUB3 Transformation)
  6. vortexsofthrms (AI HRMS & Automated Payroll Workflow)

STRICT OUTPUT FORMAT:
You MUST respond with a single, valid JSON object with NO markdown formatting, NO markdown code fences (```json), and NO extraneous commentary.
The JSON object MUST contain exactly these 5 keys:
1. "title": A high-converting, click-worthy SEO title (55-65 characters) containing the target keyword.
2. "excerpt": A compelling meta description (under 155 characters) summarizing the article with a clear call to action.
3. "body": Full article formatted in semantic HTML (using <h2>, <h3>, <p>, <ul>, <li>, <strong>, <blockquote>). Length: 1000-1400 words.
4. "image_prompt": A detailed photorealistic prompt for generating a high-converting 1200x630 featured blog banner image. Must specify modern cinematic lighting, 8k quality, futuristic IT / enterprise vibe, photorealistic, NO text, NO watermarks.
5. "linkedin_post": A complete, ready-to-publish LinkedIn post draft with an attention-grabbing hook line, context, 4 structured bullet points with emojis, a strong CTA pointing to Vortexsoft Innovations, and 5-6 strategic hashtags.
SYSTEM;

    $userPrompt = <<<USER
Write an in-depth, authoritative, AI-search-optimized blog post for:
Topic: "{$topic}"
Target Keyword: "{$targetKeyword}"

MANDATORY OPTIMIZATION REQUIREMENTS:
1. AEO (Answer Engine Optimization) & Direct Answer Opening:
   - The very first paragraph MUST directly answer the search intent in 45-60 clear, factual words.
   - Designed to win Google Featured Snippets (Position 0) and direct citations in Perplexity, ChatGPT, Claude, and Google AI Overviews.
2. Generative Engine Optimization (GEO) & Authority Citations:
   - Naturally integrate citations of Vortexsoft Innovations Private Limited, noting its ISO 27001:2013 certification, HIPAA compliance, Pune HQ, Bengaluru tech center, and relevant proprietary AI platforms (e.g. VortexEXHO, vortexsofthrms, vortexKonnect).
   - Back points with realistic industry data benchmarks and strategic perspectives.
3. Content Architecture (SEO):
   - Include 4-6 distinct H2 sections with descriptive subheadings targeting secondary search questions.
   - Use H3s for detailed workflows or architectural tiers.
   - Include bulleted lists for scannability.
4. Built-in FAQ Section with Schema Ready Markup:
   - End with an H2 section: "Frequently Asked Questions" containing 3 high-intent questions and authoritative 40-50 word answers.
5. Tone:
   - Authoritative, forward-looking, enterprise-grade, yet practical and engaging.

Output ONLY the JSON object conforming to the required schema.
USER;

    return ['system' => $systemPrompt, 'user' => $userPrompt];
}

/**
 * Shared cURL executor with SSL tolerance and robust error handling.
 */
function _ai_curl_post(string $url, array $headers, string $body, int $timeout = 40): string {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $body,
        CURLOPT_HTTPHEADER     => $headers,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_SSL_VERIFYPEER => false, // Prevents local dev CA bundle hang
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'VortexsoftAI/1.0 (+https://vortexsoftinnovations.com)',
    ]);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlErr  = curl_error($ch);

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
 * Parse JSON content from an AI response string with fallback regex extraction.
 */
function _ai_parse_json_content(string $content): array {
    $content = trim($content);
    // Strip markdown code fences if wrapped
    $content = preg_replace('/^```(?:json)?\s*/i', '', $content);
    $content = preg_replace('/\s*```\s*$/i', '', $content);
    $content = trim($content);

    $decoded = json_decode($content, true);

    // Fallback if raw newlines inside body broke standard JSON parser
    if (!is_array($decoded)) {
        $clean = preg_replace_callback('/"(body|linkedin_post|image_prompt)"\s*:\s*"(.*?)"\s*([,}])/s', function($m) {
            $val = str_replace(["\r\n", "\n", "\r", "\t"], ["\\n", "\\n", "\\n", "\\t"], $m[2]);
            return '"' . $m[1] . '":"' . $val . '"' . $m[3];
        }, $content);
        $decoded = json_decode($clean, true);
    }

    // Secondary Fallback: Regex extraction
    if (!is_array($decoded)) {
        $title         = '';
        $excerpt       = '';
        $body          = '';
        $image_prompt  = '';
        $linkedin_post = '';

        if (preg_match('/"title"\s*:\s*"([^"]+)"/', $content, $m))         $title = $m[1];
        if (preg_match('/"excerpt"\s*:\s*"([^"]+)"/', $content, $m))       $excerpt = $m[1];
        if (preg_match('/"image_prompt"\s*:\s*"([^"]+)"/', $content, $m))  $image_prompt = $m[1];
        if (preg_match('/"linkedin_post"\s*:\s*"([^"]+)"/', $content, $m)) $linkedin_post = $m[1];
        if (preg_match('/"body"\s*:\s*"(.*?)(?:"\s*,\s*"|"\s*\}|$)/s', $content, $m)) $body = $m[1];

        if ($title && $body) {
            $decoded = [
                'title'         => $title,
                'excerpt'       => $excerpt,
                'body'          => $body,
                'image_prompt'  => $image_prompt,
                'linkedin_post' => $linkedin_post
            ];
        }
    }

    if (!is_array($decoded) || empty($decoded['title']) || empty($decoded['body'])) {
        throw new RuntimeException('Provider returned malformed JSON: ' . substr($content, 0, 250));
    }

    return [
        'title'         => (string)($decoded['title'] ?? ''),
        'excerpt'       => (string)($decoded['excerpt'] ?? ''),
        'body'          => (string)($decoded['body'] ?? ''),
        'image_prompt'  => (string)($decoded['image_prompt'] ?? ''),
        'linkedin_post' => (string)($decoded['linkedin_post'] ?? ''),
    ];
}

/**
 * Generate blog content via Google Gemini (DEFAULT PRIMARY ENGINE).
 * Model: gemini-3.6-flash
 */
function generateWithGemini(array $prompt): array {
    $apiKey = defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY)
        ? GEMINI_API_KEY
        : (defined('DEFAULT_GEMINI_API_KEY') ? DEFAULT_GEMINI_API_KEY : base64_decode('QVEuQWI4Uk42S0ZPS19QX1NaZlAzemxtUGhnR2R6NWpzZHF3aXFNcjRZbm1DbmhtbkpYd1E='));

    if (empty($apiKey)) {
        throw new RuntimeException('Gemini API key is not configured. Please set GEMINI_API_KEY in Admin Settings or config/.env');
    }

    $model    = defined('GEMINI_MODEL') && GEMINI_MODEL ? GEMINI_MODEL : 'gemini-3.6-flash';
    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key=" . $apiKey;

    $fullPrompt = $prompt['system'] . "\n\n" . $prompt['user'];

    $payload = json_encode([
        'contents' => [
            ['role' => 'user', 'parts' => [['text' => $fullPrompt]]],
        ],
        'generationConfig' => [
            'responseMimeType' => 'application/json',
            'temperature'      => 0.7,
            'maxOutputTokens'  => 8192,
        ],
    ]);

    $headers = ['Content-Type: application/json'];

    $response = _ai_curl_post($endpoint, $headers, $payload, 45);
    $data     = json_decode($response, true);

    if (!isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $blockReason = $data['candidates'][0]['finishReason'] ?? ($data['promptFeedback']['blockReason'] ?? 'unknown');
        throw new RuntimeException("Gemini: content blocked or empty. Reason: {$blockReason}");
    }

    $content         = $data['candidates'][0]['content']['parts'][0]['text'];
    $result          = _ai_parse_json_content($content);
    $result['usage'] = $data['usageMetadata'] ?? null;
    return $result;
}

/**
 * Generate blog content via Groq.
 * Model: configurable via GROQ_MODEL env var (default: llama-3.3-70b-versatile)
 */
function generateWithGroq(array $prompt): array {
    $apiKey = defined('GROQ_API_KEY') && !empty(GROQ_API_KEY)
        ? GROQ_API_KEY
        : (defined('DEFAULT_GROQ_API_KEY') ? DEFAULT_GROQ_API_KEY : '');

    if (empty($apiKey)) {
        throw new RuntimeException('Groq API key is not configured. Please set GROQ_API_KEY in Admin Settings or config/.env');
    }

    $payload = json_encode([
        'model'    => defined('GROQ_MODEL') && GROQ_MODEL ? GROQ_MODEL : 'llama-3.3-70b-versatile',
        'messages' => [
            ['role' => 'system', 'content' => $prompt['system']],
            ['role' => 'user',   'content' => $prompt['user']],
        ],
        'temperature'     => 0.7,
        'max_tokens'      => 4096,
        'response_format' => ['type' => 'json_object'],
    ]);

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $apiKey,
    ];

    $response = _ai_curl_post('https://api.groq.com/openai/v1/chat/completions', $headers, $payload, 45);
    $data     = json_decode($response, true);

    if (!isset($data['choices'][0]['message']['content'])) {
        throw new RuntimeException('Groq: unexpected response shape. ' . substr($response, 0, 300));
    }

    $content         = $data['choices'][0]['message']['content'];
    $result          = _ai_parse_json_content($content);
    $result['usage'] = $data['usage'] ?? null;
    return $result;
}

/**
 * Generate blog content via OpenRouter.
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
        'max_tokens'  => 4096,
    ]);

    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . OPENROUTER_API_KEY,
        'HTTP-Referer: https://vortexsoftinnovations.com',
        'X-Title: Vortexsoft Blog Generator',
    ];

    $response = _ai_curl_post('https://openrouter.ai/api/v1/chat/completions', $headers, $payload, 45);
    $data     = json_decode($response, true);

    if (!isset($data['choices'][0]['message']['content'])) {
        throw new RuntimeException('OpenRouter: unexpected response shape. ' . substr($response, 0, 300));
    }

    $content         = $data['choices'][0]['message']['content'];
    $result          = _ai_parse_json_content($content);
    $result['usage'] = $data['usage'] ?? null;
    return $result;
}

/**
 * Generate photorealistic AI blog featured image and save to uploads/blog/.
 * Uses Pollinations AI Flux/SDXL engine.
 * Returns relative path e.g. /uploads/blog/ai_blog_xxx.jpg or null on failure.
 */
function generateBlogAiImage(string $imagePrompt, string $slugOrTopic): ?string {
    if (empty($imagePrompt)) {
        $imagePrompt = "Modern corporate technology office with AI neural network glow, photorealistic 8k, blue and dark theme";
    }

    $safeSlug = preg_replace('/[^a-zA-Z0-9\-_]/', '_', strtolower($slugOrTopic));
    $safeSlug = substr(trim($safeSlug, '_'), 0, 40) ?: 'blog';
    $filename = 'ai_blog_' . $safeSlug . '_' . time() . '.jpg';

    $uploadDir = __DIR__ . '/../uploads/blog/';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
    }
    $targetPath = $uploadDir . $filename;

    $cleanPrompt = urlencode(substr($imagePrompt, 0, 350));
    $imageUrl = "https://image.pollinations.ai/prompt/{$cleanPrompt}?width=1200&height=630&nologo=true&seed=" . rand(1000, 999999);

    $ch = curl_init($imageUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 35,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_USERAGENT      => 'VortexsoftAI/1.0 (+https://vortexsoftinnovations.com)',
    ]);
    $imgData  = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($httpCode === 200 && strlen($imgData) > 5000) {
        if (@file_put_contents($targetPath, $imgData)) {
            return '/uploads/blog/' . $filename;
        }
    }
    return null;
}

/**
 * Run all three providers and return results array.
 */
function generateAllProviders(string $topic, string $keyword): array {
    $prompt = buildBlogPrompt($topic, $keyword);

    $providers = [
        'gemini'     => fn() => generateWithGemini($prompt),
        'groq'       => fn() => generateWithGroq($prompt),
        'openrouter' => fn() => generateWithOpenRouter($prompt),
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
 * Generate exclusively with the default Gemini provider.
 */
function generateGeminiDefault(string $topic, string $keyword): array {
    $prompt = buildBlogPrompt($topic, $keyword);
    try {
        $data = generateWithGemini($prompt);
        return [
            'gemini' => ['ok' => true, 'data' => $data]
        ];
    } catch (Throwable $e) {
        return [
            'gemini' => ['ok' => false, 'error' => $e->getMessage()]
        ];
    }
}

/**
 * Format token usage for display.
 */
function formatUsage(?array $usage): string {
    if (!$usage) return '—';
    if (isset($usage['prompt_tokens'])) {
        $total = ($usage['prompt_tokens'] ?? 0) + ($usage['completion_tokens'] ?? 0);
        return number_format($total) . ' tokens (~' . number_format(($usage['completion_tokens'] ?? 0)) . ' gen)';
    }
    if (isset($usage['totalTokenCount'])) {
        return number_format($usage['totalTokenCount']) . ' tokens';
    }
    return '—';
}
