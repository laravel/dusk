<?php

namespace Laravel\Dusk\Tests;

use Laravel\Dusk\Concerns\ProvidesBrowser;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProvidesBrowserTest extends TestCase
{
    use ProvidesBrowser;

    /**
     * @dataProvider testData
     */
    public function test_capture_failures_for()
    {
        $browser = m::mock(stdClass::class);
        $browser->shouldReceive('screenshot')->with(
            'failure-Laravel_Dusk_Tests_ProvidesBrowserTest_test_capture_failures_for-0'
        );
        $browsers = collect([$browser]);

        $this->captureFailuresFor($browsers);
    }

    /**
     * @dataProvider testData
     */
    public function test_store_console_logs_for()
    {
        $browser = m::mock(stdClass::class);
        $browser->shouldReceive('storeConsoleLog')->with(
            'Laravel_Dusk_Tests_ProvidesBrowserTest_test_store_console_logs_for-0'
        );
        $browsers = collect([$browser]);

        $this->storeConsoleLogsFor($browsers);
    }

    public function testData()
    {
        return [
            ['foo'],
        ];
    }

    /**
     * Implementation of abstract ProvidesBrowser::driver().
     */
    protected function driver()
    {
    }
}
