<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp\Http\Requests;

/**
 * Class Request
 */
class Request extends BaseRequest
{
    /**
     * @return string
     */
    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');

        if ($position === false) {
            return $path;
        }

        return substr($path, 0, $position);
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        $body = [];

        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'post') {
            $postData = json_decode(file_get_contents('php://input'), true);
            foreach (($postData ?? []) as $key => $value) {
                // For JSON APIs, keep decoded scalar types intact (int/float/bool),
                // and only sanitize strings.
                if ($key === 'checked' && $value !== null) {
                    $value = (int) $value;
                }

                if (is_string($value)) {
                    $body[$key] = filter_var($value, FILTER_SANITIZE_SPECIAL_CHARS);
                } else {
                    $body[$key] = $value;
                }
            }
        }

        return $body;
    }
}