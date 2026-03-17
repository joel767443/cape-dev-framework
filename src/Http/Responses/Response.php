<?php

namespace WebApp\Http\Responses;

/**
 * Class Response
 */
class Response
{
    /**
     * @param string $message
     * @param int $code
     * @param array $data
     * @param bool $isSuccess
     * @return array
     */
    public function jsonResponse(string $message, int $code, array $data, bool $isSuccess = true): array
    {
        $this->allowOrigin();

        $this->setStatusCode($code);

        return [
            "success" => true,
            "code" => $code,
            "message" => $message,
            "data" => $data
        ];
    }

    /**
     * @param int $code
     * @return void
     */
    public function setStatusCode(int $code): void
    {
        http_response_code($code);
    }

    /**
     * API Calls will have CORS error without this
     * @TODO allow only specified URLs
     * @return void
     */
    public function allowOrigin(): void
    {
        header("Access-Control-Allow-Origin: *");

        header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE");
        header("Access-Control-Allow-Headers: Content-Type");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            // Handle pre-flight request. Respond successfully to OPTIONS requests.
            header("HTTP/1.1 200 OK");
            exit();
        }
    }
}