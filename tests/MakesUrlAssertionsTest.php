<?php

namespace Laravel\Dusk\Tests;

use Laravel\Dusk\Browser;
use Mockery as m;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;

class MakesUrlAssertionsTest extends TestCase
{
    public function test_assert_url_is()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->once()->andReturn(
            'http://www.google.com?foo=bar',
            'http://www.google.com:80/test?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertUrlIs('http://www.google.com');
        $browser->assertUrlIs('http://www.google.com:80/test');
        $browser->assertUrlIs('*google*');

        try {
            $browser->assertUrlIs('http://www.google.com');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual URL [http://www.google.com:80/test?foo=bar] does not equal expected URL [http://www.google.com].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_scheme_is()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->once()->andReturn(
            'http://www.google.com?foo=bar',
            'https://www.google.com:80/test?foo=bar',
            'ftp://www.google.com',
            'http://www.google.com',
            'http://www.google.com'
        );
        $browser = new Browser($driver);

        $browser->assertSchemeIs('http');
        $browser->assertSchemeIs('https');
        $browser->assertSchemeIs('ftp');
        $browser->assertSchemeIs('*tp*');

        try {
            $browser->assertSchemeIs('https');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual scheme [http] does not equal expected scheme [https].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_scheme_is_not()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test'.
            'https://www.google.com/test',
            'https://www.google.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertSchemeIsNot('https');
        $browser->assertSchemeIsNot('http');

        try {
            $browser->assertSchemeIsNot('https');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Scheme [https] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_host_is()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->once()->andReturn(
            'http://www.google.com?foo=bar',
            'http://google.com?foo=bar',
            'https://www.laravel.com:80/test?foo=bar',
            'https://www.laravel.com'
        );
        $browser = new Browser($driver);

        $browser->assertHostIs('www.google.com');
        $browser->assertHostIs('google.com');
        $browser->assertHostIs('www.laravel.com');

        try {
            $browser->assertHostIs('testing.com');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual host [www.laravel.com] does not equal expected host [testing\.com].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_host_is_not()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test',
            'https://www.laravel.com/test',
            'https://laravel.com/test',
            'https://laravel.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertHostIsNot('laravel.com');
        $browser->assertHostIsNot('laravel.com');
        $browser->assertHostIsNot('www.laravel.com');

        try {
            $browser->assertHostIsNot('laravel.com');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Host [laravel.com] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_port_is()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->once()->andReturn(
            'http://www.laravel.com:80/test?foo=bar',
            'https://www.laravel.com:443/test?foo=bar',
            'https://www.laravel.com',
            'https://www.laravel.com:22'
        );
        $browser = new Browser($driver);

        $browser->assertPortIs('80');
        $browser->assertPortIs('443');
        $browser->assertPortIs('');

        try {
            $browser->assertPortIs('21');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual port [22] does not equal expected port [21].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_port_is_not()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.laravel.com:80/test?foo=bar',
            'https://www.laravel.com:443/test?foo=bar',
            'https://www.laravel.com',
            'https://www.laravel.com:22'
        );
        $browser = new Browser($driver);

        $browser->assertPortIsNot('443');
        $browser->assertPortIsNot('80');
        $browser->assertPortIsNot('22');

        try {
            $browser->assertPortIsNot('22');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Port [22] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_is()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
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

        try {
            $browser->assertPathIs('foo/*/');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual path [foo/1/bar/1] does not equal expected path [foo/*/].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_begins_with()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertPathBeginsWith('/tes');

        try {
            $browser->assertPathBeginsWith('test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual path [/test] does not begin with expected path [test].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_is_not()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertPathIsNot('test');

        try {
            $browser->assertPathIsNot('/test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Path [/test] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_is()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentIs('b*z');

        try {
            $browser->assertFragmentIs('ba');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual fragment [baz] does not equal expected fragment [ba].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_begins_with()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentBeginsWith('ba');

        try {
            $browser->assertFragmentBeginsWith('Ba');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual fragment [baz] does not begin with expected fragment [Ba].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_is_not()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentIsNot('Baz');

        try {
            $browser->assertFragmentIsNot('baz');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Fragment [baz] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_route_is()
    {
        require_once __DIR__.'/stubs/route.php';

        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            '/test/1'
        );
        $browser = new Browser($driver);

        $browser->assertRouteIs('test', ['id' => 1]);

        try {
            $browser->assertRouteIs('test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Actual path [/test/1] does not equal expected path [/test/].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com',
            'http://www.google.com',
            'http://www.google.com/?foo'
        );
        $browser = new Browser($driver);

        try {
            $browser->assertQueryStringHas('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected query string in [http://www.google.com].',
                $e->getMessage()
            );
        }

        $browser->assertQueryStringHas('foo');

        try {
            $browser->assertQueryStringHas('bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected query string parameter [bar] in [http://www.google.com/?foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name_value()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertQueryStringHas('foo', 'bar');

        try {
            $browser->assertQueryStringHas('foo', '');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Query string parameter [foo] had value [bar], but expected [].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name_array_value()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/?foo[]=bar&foo[]=buzz'
        );
        $browser = new Browser($driver);

        $browser->assertQueryStringHas('foo', ['bar', 'buzz']);

        try {
            $browser->assertQueryStringHas('foo', '');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Query string parameter [foo] had value [bar,buzz], but expected [].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_missing()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com',
            'http://www.google.com/?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertQueryStringMissing('foo');
        $browser->assertQueryStringMissing('Foo');

        try {
            $browser->assertQueryStringMissing('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Found unexpected query string parameter [foo] in [http://www.google.com/?foo=bar].',
                $e->getMessage()
            );
        }
    }
}
