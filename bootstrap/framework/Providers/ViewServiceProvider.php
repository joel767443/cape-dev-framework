<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\Providers;

use DI\ContainerBuilder;
use Illuminate\Container\Container as IlluminateContainer;
use Illuminate\Contracts\View\Factory as ViewFactoryContract;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Psr\Container\ContainerInterface;
use RuntimeException;
use WebApp\Container\ServiceProviderInterface;
use WebApp\View\ViewRenderer;
use function DI\autowire;
use function DI\create;

/**
 *
 */
final class ViewServiceProvider implements ServiceProviderInterface
{
    /**
     * @param ContainerBuilder $builder
     * @return void
     */
    public function register(ContainerBuilder $builder): void
    {
        $builder->addDefinitions([
            Filesystem::class => create(Filesystem::class),

            IlluminateContainer::class => \DI\factory(function (): IlluminateContainer {
                return new IlluminateContainer();
            }),

            Dispatcher::class => \DI\factory(function (IlluminateContainer $container): Dispatcher {
                return new Dispatcher($container);
            }),

            BladeCompiler::class => \DI\factory(function (Filesystem $files): BladeCompiler {
                $compiled = (string) config('view.compiled');
                if ($compiled === '') {
                    throw new RuntimeException('Missing config: view.compiled');
                }

                return new BladeCompiler($files, $compiled);
            }),

            EngineResolver::class => \DI\factory(function (BladeCompiler $blade): EngineResolver {
                $resolver = new EngineResolver();
                $resolver->register('blade', fn () => new CompilerEngine($blade));
                return $resolver;
            }),

            FileViewFinder::class => \DI\factory(function (Filesystem $files): FileViewFinder {
                $paths = config('view.paths', []);
                if (!is_array($paths) || $paths === []) {
                    throw new RuntimeException('Missing config: view.paths');
                }

                return new FileViewFinder($files, $paths);
            }),

            ViewFactoryContract::class => \DI\factory(
                function (EngineResolver $engines, FileViewFinder $finder, Dispatcher $events, IlluminateContainer $container): Factory {
                    $factory = new Factory($engines, $finder, $events);
                    $factory->setContainer($container);
                    $factory->addExtension('blade.php', 'blade');
                    return $factory;
                }
            ),

            ViewRenderer::class => autowire(ViewRenderer::class),
        ]);
    }

    /**
     * @param ContainerInterface $container
     * @return void
     */
    public function boot(ContainerInterface $container): void
    {
        // no-op
    }
}

