<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebApp\Container\ContainerFactory;
use WebApp\Http\Kernel;
use WebApp\Http\Middleware\CorsMiddleware;
use Psr\Container\ContainerInterface;

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
    private Kernel $kernel;
    private ContainerInterface $container;

    /**
     * @param $rootPath
     */
    public function __construct($rootPath)
    {
        self::$ROOT_PATH = $rootPath;

        $this->container = ContainerFactory::build([]);

        // Keep Router API for route registration, but delegate handling to Http\Kernel.
        $this->router = new Router(new \WebApp\Http\Requests\Request(), new \WebApp\Http\Responses\Response());
        $this->kernel = new Kernel(
            $this->router->getRouteCollection(),
            [
                new CorsMiddleware(),
            ]
            ,
            $this->container
        );
    }

    /**
     * Handle a request and return a response.
     */
    public function handle(Request $request): Response
    {
        return $this->kernel->handle($request);
    }

    /**
     * Create request-from-globals, handle, and send.
     */
    public function run(): void
    {
        $request = Request::createFromGlobals();
        $response = $this->handle($request);
        $response->send();
    }
}