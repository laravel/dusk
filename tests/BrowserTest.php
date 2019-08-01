<?php

namespace Laravel\Dusk\Tests;

use stdClass;
use Mockery as m;
use Laravel\Dusk\Page;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\ExpectationFailedException;
use Facebook\WebDriver\Remote\WebDriverBrowserType;

class BrowserTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function test_visit()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit('/login');
    }

    public function test_visit_with_page_object()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);

        $this->assertEquals(['@modal' => '#modal'], $browser->resolver->elements);
        $this->assertEquals($page, $browser->page);
        $this->assertTrue($page->asserted);
    }

    public function test_on_method_sets_current_page()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->on($page = new BrowserTestPage);

        $this->assertEquals(['@modal' => '#modal'], $browser->resolver->elements);
        $this->assertEquals($page, $browser->page);
        $this->assertTrue($page->asserted);
    }

    public function test_refresh_method()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->refresh')->once();
        $browser = new Browser($driver);

        $browser->refresh();
    }

    public function test_with_method()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->with('prefix', function ($browser) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
        });
    }

    public function test_with_method_with_page()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);

        $page->asserted = false;

        $browser->with('prefix', function ($browser) use ($page) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
            $this->assertEquals($page, $browser->page);
            $this->assertFalse($page->asserted);
        });
    }

    public function test_within_method()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within('prefix', function ($browser) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
        });
    }

    public function test_within_method_with_page()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);

        $page->asserted = false;

        $browser->within('prefix', function ($browser) use ($page) {
            $this->assertInstanceof(Browser::class, $browser);
            $this->assertEquals('body prefix', $browser->resolver->prefix);
            $this->assertEquals($page, $browser->page);
            $this->assertFalse($page->asserted);
        });
    }

    public function test_page_macros()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('navigate->to')->with('http://laravel.dev/login');
        $browser = new Browser($driver);
        Browser::$baseUrl = 'http://laravel.dev';

        $browser->visit($page = new BrowserTestPage);
        $browser->doSomething();

        $this->assertTrue($browser->page->macroed);
    }

    public function test_retrieve_console()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('manage->getLog')->with('browser')->andReturnNull();
        $driver->shouldReceive('getCapabilities->getBrowserName')->andReturn(WebDriverBrowserType::CHROME);
        $browser = new Browser($driver);
        Browser::$storeConsoleLogAt = 'not-null';

        $browser->storeConsoleLog('file');
    }

    public function test_disable_console()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldNotReceive('manage');
        $driver->shouldReceive('getCapabilities->getBrowserName')->andReturnNull();
        $browser = new Browser($driver);

        $browser->storeConsoleLog('file');
    }

    public function test_assert_console_missing_errors()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageRegExp('/Console log had unexpected errors/');

        Browser::$assertConsoleLogFilter = function ($consoleLog) {
            return $consoleLog;
        };

        $driver = m::mock(stdClass::class);

        $driver->shouldReceive('getCapabilities->getBrowserName')->andReturn(WebDriverBrowserType::CHROME);

        $driver->shouldReceive('manage->getLog')->andReturn(
            [],
            [
                [
                    'level' => 'SEVERE',
                    'message' => "http://example.test/js/vendors~app.js?28f8d5a6622d03b99bae 91554:31 \"Warning: Each child in a list should have a unique \\\"key\\\" prop .%s % s See https://fb.me/react-warning-keys for more information.%s\" \"\n\nCheck the render method of `Summary`.\" \"\" \"\n    in table (created by Summary)\n    in Summary (created by Context.Consumer)\n    in Connect(Summary) (created by Route)\n    in Route (created by CreateEmployerWizard)\n    in Switch (created by CreateEmployerWizard)\n    in Transition (created by CSSTransition)",
                    'source' => 'console-api',
                    'timestamp' => 1564655342641,
                ],
            ]
        );

        $browser = new Browser($driver);

        $browser->assertConsoleLogMissingErrors();
        $browser->assertConsoleLogMissingErrors();
    }

    public function test_assert_console_missing_errors_filtered()
    {
        Browser::$assertConsoleLogFilter = function ($consoleLog) {
            $ignoreIfMessageContains = [
                'Warning: Each child in a list should have a unique \"key\" prop.',
            ];

            foreach ($ignoreIfMessageContains as $ignoreText) {
                if (stripos($consoleLog['message'], $ignoreText) !== false) {
                    return false;
                }
            }

            return true;
        };

        $driver = m::mock(stdClass::class);

        $driver->shouldReceive('getCapabilities->getBrowserName')->andReturn(WebDriverBrowserType::CHROME);

        $driver->shouldReceive('manage->getLog')->andReturn([
            [
                'level' => 'SEVERE',
                'message' => "http://example.test/js/vendors~app.js?28f8d5a6622d03b99bae 91554:31 \"Warning: Each child in a list should have a unique \\\"key\\\" prop.%s %s See https://fb.me/react-warning-keys for more information.%s\" \"\n\nCheck the render method of `Summary`.\" \"\" \"\n    in table (created by Summary)\n    in Summary (created by Context.Consumer)\n    in Connect(Summary) (created by Route)\n    in Route (created by CreateEmployerWizard)\n    in Switch (created by CreateEmployerWizard)\n    in Transition (created by CSSTransition)",
                'source' => 'console-api',
                'timestamp' => 1564655342641,
            ],
        ]);

        $browser = new Browser($driver);

        $browser->assertConsoleLogMissingErrors();
    }
}

class BrowserTestPage extends Page
{
    public $asserted = false;
    public $macroed = false;

    public function assert(Browser $browser)
    {
        $this->asserted = true;
    }

    public function url()
    {
        return '/login';
    }

    public function doSomething()
    {
        $this->macroed = true;
    }

    public static function siteElements()
    {
        return ['@modal' => '#modal'];
    }
}
