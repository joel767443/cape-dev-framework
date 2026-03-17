<?php

namespace WebApp;

use WebApp\Http\Requests\Request;
use WebApp\Http\Responses\Response;

/**
 * Class Application
 */
class Application
{
    /**
     * @var string
     */
    public static string $ROOT_PATH;
    /**
     * @var Router
     */
    public Router $router;
    /**
     * @var Request
     */
    public Request $request;
    /**
     * @var Response
     */
    public Response $response;

    /**
     * @param $rootPath
     */
    public function __construct($rootPath)
    {
        self::$ROOT_PATH = $rootPath;
        $this->request = new Request();
        $this->response = new Response();
        $this->router = new Router($this->request, $this->response);
    }

    /**
     * @return void
     */
    public function run(): void
    {
        echo json_encode($this->router->resolve());
    }
}