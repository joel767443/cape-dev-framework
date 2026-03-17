<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
namespace WebApp\View;

use Illuminate\Contracts\View\Factory as ViewFactory;

/**
 *
 */
final class ViewRenderer
{
    /**
     * @param ViewFactory $views
     */
    public function __construct(private readonly ViewFactory $views)
    {
    }

    /**
     * Render a Blade view to a string.
     *
     * @param array<string, mixed> $data
     */
    public function render(string $name, array $data = []): string
    {
        return $this->views->make($name, $data)->render();
    }
}

