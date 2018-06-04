<?php

namespace Dusk\Tests;

use Mockery;
use StdClass;
use PHPUnit\Framework\TestCase;
use Laravel\Dusk\Concerns\ProvidesBrowser;

class ProvidesBrowserTest extends TestCase
{
    use ProvidesBrowser;

    /**
     * @dataProvider testData
     */
    public function test_capture_failures_for()
    {
        $browser = Mockery::mock(StdClass::class);
        $browser->shouldReceive('screenshot')->with(
            'failure-Dusk_Tests_ProvidesBrowserTest_test_capture_failures_for-0'
        );
        $browsers = collect([$browser]);

        $this->captureFailuresFor($browsers);
    }

    /**
     * @dataProvider testData
     */
    public function test_store_console_logs_for()
    {
        $browser = Mockery::mock(StdClass::class);
        $browser->shouldReceive('storeConsoleLog')->with(
            'Dusk_Tests_ProvidesBrowserTest_test_store_console_logs_for-0'
        );
        $browsers = collect([$browser]);

        $this->storeConsoleLogsFor($browsers);
    }

    public function testData()
    {
        return [
            ['foo']
        ];
    }

    /**
     * implementation of abstract ProvidesBrowser::driver()
     */
    protected function driver()
    {
    }
}
