<?php

namespace Laravel\Dusk\Tests\Unit;

use Facebook\WebDriver\Exception\TimeOutException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriver;
use Facebook\WebDriver\WebDriverWait;
use Laravel\Dusk\Browser;
use Laravel\Dusk\ElementResolver;
use Laravel\Dusk\Tests\Concerns\SwapsUrlGenerator;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class WaitsForElementsTest extends TestCase
{
    use SwapsUrlGenerator;

    protected function tearDown(): void
    {
        m::close();
    }

    public function test_when_available()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->andReturn('bar');
        $element->shouldReceive('isDisplayed')->andReturnTrue();

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(5, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });
        $driver->shouldReceive('findElement')->andReturn($element);

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);
        $resolver->shouldReceive('findOrFail')->with('bar')->andThrow(TimeOutException::class, 'Waited 5 seconds for selector [bar].');

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

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);
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

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(0, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);
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
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(5, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        $browser->waitUsing(5, 100, function () {
            return true;
        });
    }

    public function test_wait_using_failure()
    {
        $this->expectException(TimeOutException::class);

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        $browser->waitUsing(1, 100, function () {
            return false;
        });
    }

    public function test_can_wait_for_location()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')
            ->with("return window.location.pathname == '/home';")
            ->andReturnTrue();
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        $browser->waitForLocation('/home');
    }

    public function test_can_wait_for_a_url_location()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')
            ->with('return `${location.protocol}//${location.host}${location.pathname}` == \'http://example.com/home\';')
            ->andReturnTrue();
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        $browser->waitForLocation('http://example.com/home');
    }

    public function test_can_wait_for_a_ssl_url_location()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')
            ->with('return `${location.protocol}//${location.host}${location.pathname}` == \'https://example.com/home\';')
            ->andReturnTrue();
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        $browser->waitForLocation('https://example.com/home');
    }

    public function test_can_wait_for_route()
    {
        $this->swapUrlGenerator();

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')
            ->with("return window.location.pathname == '/home/';")
            ->andReturnTrue();
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        $browser->waitForRoute('home');
    }

    public function test_can_wait_for_text()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->andReturn('Discount: 20%');

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->waitForText('Discount: 20%');
    }

    public function test_can_wait_for_text_to_go_missing()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')
            ->times(3)
            ->andReturn('Discount: 20%', 'Discount: 20%', 'SOLD OUT!');

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilMissingText('Discount: 20%');
    }

    public function test_wait_until_missing()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isDisplayed')
            ->times(2)
            ->andReturn(true, false);

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilMissing('foo');
    }

    public function test_wait_until_missing_text_failure_message_containing_a_percent_character()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->andReturn('Discount: 20%');

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->waitUntilMissingText('Discount: 20%', 1);
            $this->fail('waitUntilMissingText() did not timeout.');
        } catch (TimeOutException $e) {
            $this->assertSame('Waited 1 seconds for removal of text [Discount: 20%].', $e->getMessage());
        }
    }

    public function test_wait_for_text_failure_message_containing_a_percent_character()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->andReturn('Discount: None');

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->waitForText('Discount: 20%', 1);
            $this->fail('waitForText() did not timeout.');
        } catch (TimeOutException $e) {
            $this->assertSame('Waited 1 seconds for text [Discount: 20%].', $e->getMessage());
        }
    }

    public function test_wait_for_text_in_failure_message_containing_a_percent_character()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andThrow(TimeOutException::class, 'Waited 1 seconds for text "Discount: 20%" in selector foo');

        $browser = new Browser($driver, $resolver);

        try {
            $browser->waitForTextIn('foo', 'Discount: 20%', 1);
            $this->fail('waitForTextIn() did not timeout.');
        } catch (TimeOutException $e) {
            $this->assertSame('Waited 1 seconds for text "Discount: 20%" in selector foo', $e->getMessage());
        }
    }

    public function test_wait_for_link_failure_message_containing_a_percent_character()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')->andThrow(TimeOutException::class, 'Waited 1 seconds for link [https://laravel.com?q=foo%20bar].');
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        try {
            $browser->waitForLink('https://laravel.com?q=foo%20bar', 1);
            $this->fail('waitForLink() did not timeout.');
        } catch (TimeOutException $e) {
            $this->assertSame('Waited 1 seconds for link [https://laravel.com?q=foo%20bar].', $e->getMessage());
        }
    }

    public function test_wait_for_location_failure_message_containing_a_percent_character()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')->andThrow(TimeOutException::class, 'Waited 1 seconds for location [https://laravel.com?q=foo%20bar].');
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        try {
            $browser->waitForLocation('https://laravel.com?q=foo%20bar', 1);
            $this->fail('waitForLocation() did not timeout.');
        } catch (TimeOutException $e) {
            $this->assertSame('Waited 1 seconds for location [https://laravel.com?q=foo%20bar].', $e->getMessage());
        }
    }

    public function test_wait_for_an_element_to_be_enabled()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isEnabled')->andReturnTrue();

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')->with('#button')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilEnabled('#button', 1);
    }

    public function test_wait_for_an_element_to_be_disabled()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isEnabled')->andReturn(false);

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(1, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')->with('#button')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilDisabled('#button', 1);
    }

    public function test_wait_for_text_in()
    {
        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->andReturn('Discount: 20%');

        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->waitForTextIn('foo', 'Discount: 20%');
    }

    public function test_wait_for_link()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')
            ->times(3)
            ->andReturnTrue();
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $link = 'https://laravel.com/docs/8.x/dusk';

        $script = <<<JS
            var link = jQuery.find(`body a:contains('{$link}')`);
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
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')->andReturn('bar');
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilVue('foo', 'bar', 'foo');
    }

    public function test_wait_until_vue_is_not()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')->andReturn('bar');
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->waitUntilVueIsNot('foo', 'foo', 'foo');
    }

    public function test_wait_for_dialog()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('wait')->with(2, 100)->andReturn($driverWait = m::mock(WebDriverWait::class));
        $driverWait->shouldReceive('until')->andReturnTrue();

        $browser = new Browser($driver);

        $browser->waitForDialog();
    }

    public function test_wait_for_reload()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('executeScript')
            ->times(2)
            ->andReturnTrue();
        $driver->shouldReceive('wait')->with(2, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $browser = new Browser($driver);

        $browser->waitForReload();
    }

    public function test_wait_for_event()
    {
        $driver = m::mock(WebDriver::class);
        $driver->shouldReceive('manage->timeouts->setScriptTimeout')->with(3);
        $driver->shouldReceive('executeAsyncScript')->with(
            'eval(arguments[0]).addEventListener(arguments[1], () => arguments[2](), { once: true });',
            ['body form', 'submit']
        );
        $driver->shouldReceive('wait')->with(3, 100)->andReturnUsing(function ($seconds, $interval) use ($driver) {
            return new WebDriverWait($driver, $seconds, $interval);
        });

        $resolver = m::mock(ElementResolver::class);
        $resolver->shouldReceive('findOrFail')
            ->with('form')
            ->andReturn('body form');

        $browser = new Browser($driver, $resolver);

        $browser->waitForEvent('submit', 'form', 3);
    }
}
