<?php

namespace Laravel\Dusk\Tests;

use Facebook\WebDriver\Exception\TimeOutException;
use Laravel\Dusk\Browser;
use Laravel\Dusk\Tests\Concerns\SwapsUrlGenerator;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use stdClass;

class WaitsForElementsTest extends TestCase
{
    use SwapsUrlGenerator;

    protected function tearDown(): void
    {
        m::close();
    }

    public function test_when_available()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('bar');
        $element->shouldReceive('isDisplayed')->andReturnTrue();

        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->andReturn($element);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->whenAvailable('foo', function ($foo) {
            $foo->assertSee('bar');
        });

        try {
            $browser->whenAvailable('bar', function ($bar) {
                // Callback not fired as selector not found
            });
        } catch (TimeOutException $e) {
            $this->assertSame('Waited 5 seconds for selector [bar].', $e->getMessage());
        }
    }

    public function test_default_wait_time()
    {
        Browser::$waitSeconds = 2;

        $browser = new Browser(new stdClass);
        $then = microtime(true);

        try {
            $browser->waitUsing(null, 100, function () {
                return false;
            });
        } catch (TimeOutException $e) {
            //
        }

        $this->assertSame(2.0, floor(microtime(true) - $then));
    }

    public function test_default_wait_time_can_be_overridden()
    {
        Browser::$waitSeconds = 2;

        $browser = new Browser(new stdClass);
        $then = microtime(true);

        try {
            $browser->waitUsing(0, 100, function () {
                return true;
            });
        } catch (TimeOutException $e) {
            //
        }

        $this->assertSame(0.0, floor(microtime(true) - $then));
    }

    public function test_wait_using()
    {
        $browser = new Browser(new stdClass);

        $browser->waitUsing(5, 100, function () {
            return true;
        });
    }

    public function test_wait_using_failure()
    {
        $this->expectException(TimeOutException::class);

        $browser = new Browser(new stdClass);

        $browser->waitUsing(1, 100, function () {
            return false;
        });
    }

    public function test_can_wait_for_location()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->with("return window.location.pathname == '/home';")
            ->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForLocation('/home');
    }

    public function test_can_wait_for_a_url_location()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->with('return `${location.protocol}//${location.host}${location.pathname}` == \'http://example.com/home\';')
            ->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForLocation('http://example.com/home');
    }

    public function test_can_wait_for_a_ssl_url_location()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->with('return `${location.protocol}//${location.host}${location.pathname}` == \'https://example.com/home\';')
            ->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForLocation('https://example.com/home');
    }

    public function test_can_wait_for_route()
    {
        $this->swapUrlGenerator();

        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->with("return window.location.pathname == '/home/';")
            ->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForRoute('home');
    }

    public function test_can_wait_for_text()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('Discount: 20%');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser(new stdClass, $resolver);

        $browser->waitForText('Discount: 20%');
    }

    public function test_can_wait_for_text_to_go_missing()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')
            ->times(3)
            ->andReturn('Discount: 20%', 'Discount: 20%', 'SOLD OUT!');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser(new stdClass, $resolver);

        $browser->waitUntilMissingText('Discount: 20%');
    }

    public function test_wait_until_missing()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('isDisplayed')
            ->times(2)
            ->andReturn(true, false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser(stdClass::class, $resolver);

        $browser->waitUntilMissing('foo');
    }

    public function test_wait_for_text_failure_message_containing_a_percent_character()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('Discount: None');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser(new stdClass, $resolver);

        try {
            $browser->waitForText('Discount: 20%', 1);
            $this->fail('waitForText() did not timeout.');
        } catch (TimeOutException $e) {
            $this->assertSame('Waited 1 seconds for text [Discount: 20%].', $e->getMessage());
        }
    }

    public function test_wait_for_an_element_to_be_enabled()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('isEnabled')->andReturnTrue();

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('findOrFail')->with('#button')->andReturn($element);

        $browser = new Browser(new stdClass, $resolver);

        $browser->waitUntilEnabled('#button', 1);
    }

    public function test_wait_for_an_element_to_be_disabled()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('isEnabled')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('findOrFail')->with('#button')->andReturn($element);

        $browser = new Browser(new stdClass, $resolver);

        $browser->waitUntilDisabled('#button', 1);
    }

    public function test_wait_for_text_in()
    {
        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('Discount: 20%');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser(stdClass::class, $resolver);

        $browser->waitForTextIn('foo', 'Discount: 20%');
    }

    public function test_wait_for_link()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->times(2)
            ->andReturnTrue();

        $link = 'https://laravel.com/docs/8.x/dusk';

        $script = <<<JS
            var link = jQuery.find("body a:contains(\'{$link}\')");
            return link.length > 0 && jQuery(link).is(':visible');
JS;

        $driver->shouldReceive('executeScript')
            ->with($script)
            ->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForLink($link);
    }

    public function test_wait_until_vue()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->andReturn('bar');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilVue('foo', 'bar', 'foo');
    }

    public function test_wait_until_vue_is_not()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->andReturn('bar');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilVueIsNot('foo', 'foo', 'foo');
    }

    public function test_wait_for_dialog()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('wait')->andReturn($driver);
        $driver->shouldReceive('until')->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForDialog();
    }

    public function test_wait_for_reload()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->times(2)
            ->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForReload();
    }
}
