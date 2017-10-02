<?php

use Laravel\Dusk\Browser;
use PHPUnit\Framework\TestCase;

class MakesAssertionsTest extends TestCase
{
    private $driver;
    private $browser;

    public function setUp()
    {
        $this->driver = Mockery::mock(StdClass::class);
        $this->browser = new Browser($this->driver);
    }

    public function test_assert_path_is()
    {
        $this->driver->shouldReceive('getCurrentURL')->once()->andReturn(
            '/foo',
            'foo/bar',
            'foo/1/bar',
            'foo/1/bar/1'
        );

        $this->browser->assertPathIs('/foo')
            ->assertPathIs('foo/bar')
            ->assertPathIs('foo/*/bar')
            ->assertPathIs('foo/*/bar/*')
            ->assertPathIs('foo/1/bar/1');
    }

    public function test_assert_path_begins_with()
    {
        $this->driver->shouldReceive('getCurrentURL')->once()->andReturn(
            'foo/1/bar/1',
            'foo/1/bar/1',
            '/foo/1/bar/1',
            '/foo/1/bar/1'
        );

        $this->browser->assertPathBeginsWith('foo')
            ->assertPathBeginsWith('foo/1/b')
            ->assertPathBeginsWith('/foo')
            ->assertPathBeginsWith('/foo/1/b');
    }

    public function test_assert_path_is_not()
    {
        $this->driver->shouldReceive('getCurrentURL')->andReturn('/foo/1/bar');

        $this->browser->assertPathIsNot('/foo/2/bar')
            ->assertPathIsNot('foo/1/bar');
    }

    public function test_assert_query_string_has()
    {
        $this->driver->shouldReceive('getCurrentURL')->andReturn(
            'path?hello=world&foo=bar'
        );

        $this->browser->assertQueryStringHas('foo')
            ->assertQueryStringHas('foo', 'bar')
            ->assertQueryStringHas('hello', 'world');
    }

    public function test_assert_query_string_missing()
    {
        $this->driver->shouldReceive('getCurrentURL')->andReturn(
            'path?hello=world'
        );

        $this->browser->assertQueryStringMissing('foo');
    }

    public function test_assert_title()
    {
        $this->driver->shouldReceive('getTitle')->once()->andReturn(
            'Foo',
            'Bar'
        );

        $this->browser->assertTitle('Foo')
            ->assertTitle('Bar');
    }

    public function test_assert_see()
    {
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('getText')->andReturn('Foo');

        $this->driver->shouldReceive('findElement')->andReturn($element);

        $this->browser->assertSee('Foo');
    }

    public function test_dont_assert_see()
    {
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('getText')->andReturn('Foo');

        $this->driver->shouldReceive('findElement')->andReturn($element);

        $this->browser->assertDontSee('Bar');
    }

    public function test_assert_see_link()
    {
        $linkText = 'Foo';

        $this->driver->shouldReceive('executeScript')
            ->with('return window.jQuery == null')->andReturn(false);

        $script = <<<JS
            var link = jQuery.find("body a:contains(\'$linkText\')");
            return link.length > 0 && jQuery(link).is(':visible');
JS;

        $this->driver->shouldReceive('executeScript')
            ->with($script)->andReturn(true);

        $this->browser->assertSeeLink($linkText);
    }

    public function test_assert_title_contains()
    {
        $this->driver->shouldReceive('getTitle')->andReturn('Foo Bar');

        $this->browser->assertTitleContains('Foo')
            ->assertTitleContains('Bar')
            ->assertTitleContains('o B')
            ->assertTitleContains('Foo Bar');
    }
}
