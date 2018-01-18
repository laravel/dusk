<?php

use Laravel\Dusk\Browser;
use PHPUnit\Framework\TestCase;

class MakesAssertionsTest extends TestCase
{
    public function test_assert_path_is()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->once()->andReturn(
            '/foo',
            'foo/bar',
            'foo/1/bar',
            'foo/1/bar/1'
        );
        $browser = new Browser($driver);

        $browser->assertPathIs('/foo');
        $browser->assertPathIs('foo/bar');
        $browser->assertPathIs('foo/*/bar');
        $browser->assertPathIs('foo/*/bar/*');
        $browser->assertPathIs('foo/1/bar/1');
    }

    public function test_assert_url_is()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->once()->andReturn(
            'http://www.google.com?foo=bar',
            'http://www.google.com:80/test?foo=bar',
            'http://www.google.com:80/test?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertUrlIs('http://www.google.com');
        $browser->assertUrlIs('http://www.google.com:80/test');
        $browser->assertUrlIs('*google*');
    }
}
