<?php

use Laravel\Dusk\Browser;
use PHPUnit\Framework\ExpectationFailedException;
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

    public function test_assert_query_string_has_name()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com',
            'http://www.google.com',
            'http://www.google.com?foo'
        );
        $browser = new Browser($driver);

        try {
            $browser->assertQueryStringHas('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringStartsWith(
                'Did not see expected query string in [http://www.google.com].',
                $e->getMessage()
            );
        }

        $browser->assertQueryStringHas('foo');

        try {
            $browser->assertQueryStringHas('bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringStartsWith(
                'Did not see expected query string parameter [bar] in [http://www.google.com?foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertQueryStringHas('foo', 'bar');

        try {
            $browser->assertQueryStringHas('foo', '');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringStartsWith(
                'Query string parameter [foo] had value [bar], but expected [].',
                $e->getMessage()
            );
        }
    }
}
