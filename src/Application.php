<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */

namespace WebApp;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WebApp\Container\ContainerFactory;
use WebApp\Config\ConfigLoader;
use WebApp\Config\ConfigRepository;
use WebApp\Config\Env;
use WebApp\Http\Kernel;
use Psr\Container\ContainerInterface;
use WebApp\Providers\HttpServiceProvider;
use WebApp\Providers\RoutingServiceProvider;
use WebApp\Providers\ValidationServiceProvider;
use WebApp\Providers\LoggingServiceProvider;
use WebApp\Providers\EventsServiceProvider;
use WebApp\Http\Middleware\ExceptionHandlingMiddleware;
use WebApp\Http\Middleware\MiddlewareRegistry;
use WebApp\Http\Middleware\CorsMiddleware;
use WebApp\Http\Middleware\ErrorLoggingMiddleware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use WebApp\Validation\RequestValidator;

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

        Env::load((string) $rootPath);

        $configRepo = new ConfigRepository();
        $configLoader = new ConfigLoader();
        $configRepo->setMany($configLoader->loadDir((string) $rootPath . DIRECTORY_SEPARATOR . 'config'));
        $GLOBALS['__webapp_config'] = $configRepo;

        $this->container = ContainerFactory::build([
            new HttpServiceProvider(),
            new RoutingServiceProvider(),
            new LoggingServiceProvider(),
            new ValidationServiceProvider(),
            new EventsServiceProvider(),
        ]);

        // Keep Router API for route registration, but delegate handling to Http\Kernel.
        $this->router = new Router(new \WebApp\Http\Requests\Request(), new \WebApp\Http\Responses\Response());

        $this->kernel = new Kernel(
            $this->router->getRouteCollection(),
            [
                // Keep ordering explicit: exception handling should wrap everything.
                $this->container->get(ExceptionHandlingMiddleware::class),
                $this->container->get(ErrorLoggingMiddleware::class),
                $this->container->get(CorsMiddleware::class),
            ]
            ,
            $this->container,
            $this->container->get(MiddlewareRegistry::class),
            $this->container->get(EventDispatcherInterface::class),
            $this->container->get(RequestValidator::class)
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