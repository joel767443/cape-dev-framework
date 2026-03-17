<?php

/**
 * @author Yoweli Kachala <yowelikachala@gmail.com>
 */
use PHPUnit\Framework\TestCase;
use WebApp\Application;

/**
 *
 */
final class ViewRendererTest extends TestCase
{
    /**
     * @return void
     */
    public function testItRendersBladeTemplateWithVariables(): void
    {
        require_once __DIR__ . '/../autoload.php';

        $app = new Application(dirname(__DIR__));

        $html = view('emails.example', [
            'title' => 'Welcome',
            'name' => 'Ada',
        ]);

        self::assertStringContainsString('<h1>Welcome</h1>', $html);
        self::assertStringContainsString('Hello, Ada.', $html);
    }
}

