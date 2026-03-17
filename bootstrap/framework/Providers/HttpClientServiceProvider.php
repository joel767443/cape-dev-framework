<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Providers;

use DI\ContainerBuilder;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use WebApp\Container\ServiceProviderInterface;
use WebApp\Http\Client\HttpClient;
use function DI\autowire;
use function DI\factory;

/**
 *
 */
final class HttpClientServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            ClientInterface::class => factory(function (): ClientInterface {
                $baseUri = (string) config('http.base_uri', '');
                $timeout = (float) config('http.timeout', 10.0);

                $opts = [
                    'timeout' => $timeout,
                    // Don't throw exceptions on 4xx/5xx; return the response.
                    'http_errors' => false,
                ];
                if ($baseUri !== '') {
                    $opts['base_uri'] = $baseUri;
                }

                return new Client($opts);
            }),

            HttpClient::class => autowire(HttpClient::class),
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(ContainerInterface $container): void
    {
    }
}

