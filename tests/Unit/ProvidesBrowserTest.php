<?php

namespace Laravel\Dusk\Tests\Unit;

use Laravel\Dusk\Concerns\ProvidesBrowser;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use stdClass;

class ProvidesBrowserTest extends TestCase
{
    use ProvidesBrowser;

    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * @dataProvider testData
     */
    public function test_capture_failures_for()
    {
        $browser = m::mock(stdClass::class);
        $browser->shouldReceive('screenshot')->with(
            'failure-Laravel_Dusk_Tests_Unit_ProvidesBrowserTest_test_capture_failures_for_foo-0'
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
            'Laravel_Dusk_Tests_Unit_ProvidesBrowserTest_test_store_console_logs_for_foo-0'
        );
        $browsers = collect([$browser]);

        $this->storeConsoleLogsFor($browsers);
    }

    public function test_truncate_test_name_where_that_name_is_too_long_and_might_cause_issues()
    {
        $browser = m::mock(stdClass::class);
        $browser->shouldReceive('storeConsoleLog')->with(
            'Laravel_Dusk_Tests_Unit_ProvidesBrowserTest_test_truncate_test_name_where_that_name_is_too_long_and_might_cause_is-0'
        );
        $browsers = collect([$browser]);

        $this->storeConsoleLogsFor($browsers);
    }

    public static function testData()
    {
        return [
            'foo' => ['foo'],
        ];
    }

    /**
     * Implementation of abstract ProvidesBrowser::driver().
     */
    protected function driver()
    {
    }
}
