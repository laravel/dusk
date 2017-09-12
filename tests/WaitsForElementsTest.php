<?php

use Carbon\Carbon;
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

    public function test_default_wait_time_can_be_overriden()
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
}
