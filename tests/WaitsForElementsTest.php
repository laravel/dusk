<?php

namespace Laravel\Dusk\Tests;

use Facebook\WebDriver\Exception\TimeOutException;
use Laravel\Dusk\Browser;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use stdClass;

class WaitsForElementsTest extends TestCase
{
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

        $this->assertEquals(2, floor(microtime(true) - $then));
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

        $this->assertEquals(0, floor(microtime(true) - $then));
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
        $driver->shouldReceive('executeScript')->with("return window.location.pathname == '/home';")->andReturnTrue();
        $browser = new Browser($driver);

        $browser->waitForLocation('/home');
    }

    public function test_can_wait_for_route()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->with("return window.location.pathname == '/home/';")->andReturnTrue();
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
            $this->assertEquals('Waited 1 seconds for text [Discount: 20%].', $e->getMessage());
        }
    }
}
