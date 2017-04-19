<?php

use Laravel\Dusk\Browser;
use PHPUnit\Framework\TestCase;

class WaitsForElementsTest extends TestCase
{
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
