<?php

use Facebook\WebDriver\Exception\TimeOutException;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\TestCase;

class WaitsForElementsTest extends TestCase
{
    public function test_default_wait_time()
    {
        Browser::$waitSeconds = 2;

        $browser = new Browser(new StdClass);
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

        $browser = new Browser(new StdClass);
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
        $browser = new Browser(new StdClass);

        $browser->waitUsing(5, 100, function () {
            return true;
        });
    }

    /**
     * @expectedException \Facebook\WebDriver\Exception\TimeOutException
     */
    public function test_wait_using_failure()
    {
        $browser = new Browser(new StdClass);

        $browser->waitUsing(1, 100, function () {
            return false;
        });
    }

    public function test_can_wait_for_location()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with("return window.location.pathname == '/home';")->andReturnTrue();
        $browser = new Browser($driver);

        $browser->waitForLocation('/home');
    }

    public function test_can_wait_for_route()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with("return window.location.pathname == '/home/';")->andReturnTrue();
        $browser = new Browser($driver);

        $browser->waitForRoute('home');
    }

    public function test_can_wait_for_text()
    {
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('getText')->andReturn('Discount: 20%');
        $resolver = Mockery::mock(StdClass::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);
        $browser = new Browser(new StdClass, $resolver);

        $browser->waitForText('Discount: 20%');
    }

    public function test_wait_for_text_failure_message_containing_a_percent_character()
    {
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('getText')->andReturn('Discount: None');
        $resolver = Mockery::mock(StdClass::class);
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);
        $browser = new Browser(new StdClass, $resolver);

        try {
            $browser->waitForText('Discount: 20%', 1);
            $this->fail('waitForText() did not timeout.');
        } catch (TimeOutException $e) {
            $this->assertEquals('Waited 1 seconds for text [Discount: 20%].', $e->getMessage());
        }
    }
}
