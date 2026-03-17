<?php

namespace WebApp\Http\Client;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

final class HttpClient
{
    public function __construct(private readonly ClientInterface $client)
    {
    }

    /**
     * @param array<string, mixed> $options
     * @return array{status: int, headers: array<string, string[]>, body: string}
     */
    public function request(string $method, string $url, array $options = []): array
    {
        $res = $this->client->request($method, $url, $options);
        return $this->normalizeResponse($res);
    }

    /**
     * @param array<string, mixed> $options
     * @return array{status: int, headers: array<string, string[]>, data: mixed, body: string}
     */
    public function requestJson(string $method, string $url, array $options = []): array
    {
        $options['headers'] = array_merge(
            ['Accept' => 'application/json'],
            (array) ($options['headers'] ?? [])
        );

        $res = $this->client->request($method, $url, $options);
        $normalized = $this->normalizeResponse($res);

        $body = $normalized['body'];
        $data = null;
        if ($body !== '') {
            $decoded = json_decode($body, true);
            $data = json_last_error() === JSON_ERROR_NONE ? $decoded : null;
        }

        return [
            'status' => $normalized['status'],
            'headers' => $normalized['headers'],
            'data' => $data,
            'body' => $body,
        ];
    }

    /**
     * @return array{status: int, headers: array<string, string[]>, body: string}
     */
    private function normalizeResponse(ResponseInterface $res): array
    {
        return [
            'status' => $res->getStatusCode(),
            'headers' => $res->getHeaders(),
            'body' => (string) $res->getBody(),
        ];
    }
}

