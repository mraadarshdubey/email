<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Psr\Http\Message\ResponseInterface;
use RuntimeException;

class AiEmailComposer
{
    protected Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => rtrim(config('services.nvidia.base_url'), '/') . '/',
            'timeout' => 45,
        ]);
    }

    /**
     * @return array{subject: string, body: string}
     */
    public function draft(string $brief): array
    {
        $apiKey = config('services.nvidia.api_key');

        if (! $apiKey) {
            throw new RuntimeException('NVIDIA_API_KEY is not configured.');
        }

        $response = $this->postWithRetry($apiKey, $brief);

        $payload = json_decode((string) $response->getBody(), true);
        $content = $payload['choices'][0]['message']['content'] ?? null;

        if (! $content) {
            throw new RuntimeException('AI service returned an empty response.');
        }

        $parsed = json_decode(trim($content), true);

        if (! is_array($parsed) || ! isset($parsed['subject'], $parsed['body'])) {
            // Model didn't follow the JSON contract — fall back to using the raw text as the body.
            return [
                'subject' => '',
                'body' => '<p>' . nl2br(e(trim($content))) . '</p>',
            ];
        }

        return [
            'subject' => (string) $parsed['subject'],
            'body' => (string) $parsed['body'],
        ];
    }

    /**
     * The model endpoint occasionally returns a transient 503 while scaling
     * up — one retry with a short backoff clears most of those.
     */
    private function postWithRetry(string $apiKey, string $brief, int $attempt = 1): ResponseInterface
    {
        try {
            return $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => config('services.nvidia.model'),
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are an email copywriter for Sendpeak, a bulk email marketing tool. '
                                . 'Respond with ONLY minified JSON in the exact shape {"subject":"...","body":"..."} '
                                . 'and nothing else — no markdown, no code fences, no commentary. '
                                . 'The body must be simple HTML using only p, br, strong, em, ul, li, a tags '
                                . '(no html/head/body wrapper, no inline styles, no scripts). '
                                . 'Keep it concise and ready to send as-is.',
                        ],
                        [
                            'role' => 'user',
                            'content' => $brief,
                        ],
                    ],
                    'max_tokens' => 900,
                    'temperature' => 0.7,
                ],
            ]);
        } catch (ServerException $e) {
            if ($attempt >= 2) {
                throw $e;
            }

            usleep(500_000);

            return $this->postWithRetry($apiKey, $brief, $attempt + 1);
        }
    }
}
