<?php

use Facebook\WebDriver\Cookie;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

class MakesAssertionsTest extends TestCase
{
    public function test_assert_title()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getTitle')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertTitle('foo');

        try {
            $browser->assertTitle('Foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected title [Foo] does not equal actual title [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_title_contains()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getTitle')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertTitleContains('fo');

        try {
            $browser->assertTitleContains('Fo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected value [Fo] within title [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_url_is()
    {
        $driver = Mockery::mock(StdClass::class);
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
            $this->assertContains(
                'Actual URL [http://www.google.com:80/test?foo=bar] does not equal expected URL [http://www.google.com].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_is()
    {
        $driver = Mockery::mock(StdClass::class);
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
            $this->assertContains(
                'Actual path [foo/1/bar/1] does not equal expected path [foo/*/].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_begins_with()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertPathBeginsWith('/tes');

        try {
            $browser->assertPathBeginsWith('test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual path [/test] does not begin with expected path [test].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_path_is_not()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/test'
        );
        $browser = new Browser($driver);

        $browser->assertPathIsNot('test');

        try {
            $browser->assertPathIsNot('/test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Path [/test] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_is()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentIs('b*z');

        try {
            $browser->assertFragmentIs('ba');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual fragment [baz] does not equal expected fragment [ba].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_begins_with()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentBeginsWith('ba');

        try {
            $browser->assertFragmentBeginsWith('Ba');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual fragment [baz] does not begin with expected fragment [Ba].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_fragment_is_not()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.location.href;')->andReturn(
            'http://www.google.com/#baz'
        );
        $browser = new Browser($driver);

        $browser->assertFragmentIsNot('Baz');

        try {
            $browser->assertFragmentIsNot('baz');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Fragment [baz] should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_route_is()
    {
        require_once __DIR__.'/stubs/route.php';

        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            '/test/1'
        );
        $browser = new Browser($driver);

        $browser->assertRouteIs('test', ['id' => 1]);

        try {
            $browser->assertRouteIs('test');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Actual path [/test/1] does not equal expected path [/test/].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name()
    {
        $driver = Mockery::mock(StdClass::class);
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
            $this->assertContains(
                'Did not see expected query string in [http://www.google.com].',
                $e->getMessage()
            );
        }

        $browser->assertQueryStringHas('foo');

        try {
            $browser->assertQueryStringHas('bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected query string parameter [bar] in [http://www.google.com/?foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_has_name_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getCurrentURL')->andReturn(
            'http://www.google.com/?foo=bar'
        );
        $browser = new Browser($driver);

        $browser->assertQueryStringHas('foo', 'bar');

        try {
            $browser->assertQueryStringHas('foo', '');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Query string parameter [foo] had value [bar], but expected [].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_query_string_missing()
    {
        $driver = Mockery::mock(StdClass::class);
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
            $this->assertContains(
                'Found unexpected query string parameter [foo] in [http://www.google.com/?foo=bar].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_has_cookie()
    {
        require_once __DIR__.'/stubs/decrypt.php';

        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('manage->getCookieNamed')->with('foo')->andReturn(
            new Cookie('foo', 's%3A0%3A%22%22%3B'), // ""
            null
        );
        $browser = new Browser($driver);

        $browser->assertHasCookie('foo');

        try {
            $browser->assertHasCookie('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not find expected cookie [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_cookie_missing()
    {
        require_once __DIR__.'/stubs/decrypt.php';

        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('manage->getCookieNamed')->with('foo')->andReturn(
            null,
            new Cookie('foo', 's%3A0%3A%22%22%3B') // ""
        );
        $browser = new Browser($driver);

        $browser->assertCookieMissing('foo');

        try {
            $browser->assertCookieMissing('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Found unexpected cookie [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_cookie_value()
    {
        require_once __DIR__.'/stubs/decrypt.php';

        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('manage->getCookieNamed')->with('foo')->andReturn(
            new Cookie('foo', '%25'), // "%"
            new Cookie('foo', 's%3A1%3A%22%25%22%3B') // "%"
        );
        $browser = new Browser($driver);

        $browser->assertCookieValue('foo', '%', false);
        $browser->assertCookieValue('foo', '%');

        try {
            $browser->assertCookieValue('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Cookie [foo] had value [%], but expected [bar].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_plain_cookie_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('manage->getCookieNamed')->with('foo')->andReturn(
            new Cookie('foo', '%25') // "%"
        );
        $browser = new Browser($driver);

        $browser->assertPlainCookieValue('foo', '%');

        try {
            $browser->assertPlainCookieValue('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Cookie [foo] had value [%], but expected [bar].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_see()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getText')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertSee('foo');

        try {
            $browser->assertSee('Foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected text [Foo] within element [body].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dont_see()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getText')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertDontSee('Foo');

        try {
            $browser->assertDontSee('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Saw unexpected text [foo] within element [body].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_see_in()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getText')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertSeeIn('div', 'foo');

        try {
            $browser->assertSeeIn('div', 'Foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected text [Foo] within element [body div].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dont_see_in()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getText')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertDontSeeIn('div', 'Foo');

        try {
            $browser->assertDontSeeIn('div', 'foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Saw unexpected text [foo] within element [body div].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_source_has()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getPageSource')->andReturn(
            '<p>foo</p>'
        );
        $browser = new Browser($driver);

        $browser->assertSourceHas('foo');

        try {
            $browser->assertSourceHas('Foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not find expected source code [Foo]',
                $e->getMessage()
            );
        }
    }

    public function test_assert_source_missing()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('getPageSource')->andReturn(
            '<p>foo</p>'
        );
        $browser = new Browser($driver);

        $browser->assertSourceMissing('Foo');

        try {
            $browser->assertSourceMissing('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Found unexpected source code [foo]',
                $e->getMessage()
            );
        }
    }

    public function test_assert_see_link()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(
            false, // jQuery
            true,
            false, // jQuery
            false
        );
        $browser = new Browser($driver);

        $browser->assertSeeLink('foo');

        try {
            $browser->assertSeeLink('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Did not see expected link [foo] within [body].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dont_see_link()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(
            false, // jQuery
            false,
            false, // jQuery
            true
        );
        $browser = new Browser($driver);

        $browser->assertDontSeeLink('foo');

        try {
            $browser->assertDontSeeLink('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Saw unexpected link [foo] within [body].',
                $e->getMessage()
            );
        }
    }

    public function test_see_link()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->with('return window.jQuery == null')->andReturn(false);
        $driver->shouldReceive('executeScript')->with(Mockery::on(function ($argument) {
            return Str::contains($argument, "body a:contains(\'foo\')");
        }))->andReturn(
            true
        );
        $browser = new Browser($driver);

        $this->assertTrue($browser->seeLink('foo'));
    }

    public function test_assert_input_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('getTagName')->andReturn('input');
        $element->shouldReceive('getAttribute')->andReturn(
            'value'
        );
        $driver->shouldReceive('findElement')->andReturn($element);
        $browser = new Browser($driver);

        $browser->assertInputValue('foo', 'value');

        try {
            $browser->assertInputValue('foo', 'Value');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected value [Value] for the [foo] input does not equal the actual value [value]',
                $e->getMessage()
            );
        }
    }

    public function test_assert_input_value_is_not()
    {
        $driver = Mockery::mock(StdClass::class);
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('getTagName')->andReturn('input');
        $element->shouldReceive('getAttribute')->andReturn(
            'value'
        );
        $driver->shouldReceive('findElement')->andReturn($element);
        $browser = new Browser($driver);

        $browser->assertInputValueIsNot('foo', 'Value');

        try {
            $browser->assertInputValueIsNot('foo', 'value');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Value [value] for the [foo] input should not equal the actual value.',
                $e->getMessage()
            );
        }
    }

    public function test_input_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('getTagName')->andReturn(
            'input',
            'p'
        );
        $element->shouldReceive('getAttribute')->with('value')->andReturn(
            'value'
        );
        $element->shouldReceive('getText')->andReturn(
            'text'
        );
        $driver->shouldReceive('findElement')->andReturn($element);
        $browser = new Browser($driver);

        $this->assertEquals('value', $browser->inputValue('foo'));
        $this->assertEquals('text', $browser->inputValue('foo'));
    }

    public function test_assert_checked()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->isSelected')->andReturn(
            true,
            false
        );
        $browser = new Browser($driver);

        $browser->assertChecked('foo');

        try {
            $browser->assertChecked('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected checkbox [foo] to be checked, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_checked()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->isSelected')->andReturn(
            false,
            true
        );
        $browser = new Browser($driver);

        $browser->assertNotChecked('foo');

        try {
            $browser->assertNotChecked('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Checkbox [foo] was unexpectedly checked.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_radio_selected()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->isSelected')->andReturn(
            true,
            false
        );
        $browser = new Browser($driver);

        $browser->assertRadioSelected('foo', 'bar');

        try {
            $browser->assertRadioSelected('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected radio [foo] to be selected, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_radio_not_selected()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->isSelected')->andReturn(
            false,
            true
        );
        $browser = new Browser($driver);

        $browser->assertRadioNotSelected('foo', 'bar');

        try {
            $browser->assertRadioNotSelected('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Radio [foo] was unexpectedly selected.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_selected()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getAttribute')->andReturn(
            'bar'
        );
        $browser = new Browser($driver);

        $browser->assertSelected('foo', 'bar');

        try {
            $browser->assertSelected('foo', 'Bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected value [Bar] to be selected for [foo], but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_selected()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getAttribute')->andReturn(
            'bar'
        );
        $browser = new Browser($driver);

        $browser->assertNotSelected('foo', 'Bar');

        try {
            $browser->assertNotSelected('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Unexpected value [bar] selected for [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_has_options()
    {
        $driver = Mockery::mock(StdClass::class);
        $option = Mockery::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')->with('value')->andReturn(
            'bar'
        );
        $driver->shouldReceive('findElement->findElements')->andReturn(
            [$option, $option]
        );
        $browser = new Browser($driver);

        $browser->assertSelectHasOptions('foo', ['bar']);

        try {
            $browser->assertSelectHasOptions('foo', ['bar', 'baz']);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected options [bar,baz] for selection field [foo] to be available.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_missing_options()
    {
        $driver = Mockery::mock(StdClass::class);
        $option = Mockery::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')->with('value')->andReturn(
            'bar'
        );
        $driver->shouldReceive('findElement->findElements')->andReturn(
            [],
            [$option]
        );
        $browser = new Browser($driver);

        $browser->assertSelectMissingOptions('foo', ['bar']);

        try {
            $browser->assertSelectMissingOptions('foo', ['bar', 'baz']);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Unexpected options [bar,baz] for selection field [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_has_option()
    {
        $driver = Mockery::mock(StdClass::class);
        $option = Mockery::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')->with('value')->andReturn(
            'bar'
        );
        $driver->shouldReceive('findElement->findElements')->andReturn(
            [$option],
            []
        );
        $browser = new Browser($driver);

        $browser->assertSelectHasOption('foo', 'bar');

        try {
            $browser->assertSelectHasOption('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected options [bar] for selection field [foo] to be available.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_missing_option()
    {
        $driver = Mockery::mock(StdClass::class);
        $option = Mockery::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')->with('value')->andReturn(
            'bar'
        );
        $driver->shouldReceive('findElement->findElements')->andReturn(
            [],
            [$option]
        );
        $browser = new Browser($driver);

        $browser->assertSelectMissingOption('foo', 'bar');

        try {
            $browser->assertSelectMissingOption('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Unexpected options [bar] for selection field [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_selected()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getAttribute')->with('value')->andReturn(
            'bar'
        );
        $browser = new Browser($driver);

        $this->assertTrue($browser->selected('foo', 'bar'));
        $this->assertFalse($browser->selected('foo', 'Bar'));
    }

    public function test_assert_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->getAttribute')->with('value')->andReturn(
            'bar'
        );
        $browser = new Browser($driver);

        $browser->assertValue('foo', 'bar');

        try {
            $browser->assertValue('foo', 'Bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Failed asserting that two strings are equal.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_visible()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->isDisplayed')->andReturn(
            true,
            false
        );
        $browser = new Browser($driver);

        $browser->assertVisible('foo');

        try {
            $browser->assertVisible('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Element [body foo] is not visible.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_present()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->andReturn(
            'element',
            null
        );
        $browser = new Browser($driver);

        $browser->assertPresent('foo');

        try {
            $browser->assertPresent('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Element [body foo] is not present.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_missing()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andThrow(NoSuchElementException::class);
        $element = Mockery::mock(StdClass::class);
        $element->shouldReceive('isDisplayed')->andReturn(
            false,
            true
        );
        $driver->shouldReceive('findElement')->andReturn($element);
        $browser = new Browser($driver);

        $browser->assertMissing('foo'); // Missing element.
        $browser->assertMissing('foo'); // Hidden element.

        try {
            $browser->assertMissing('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Saw unexpected element [body foo]',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dialog_opened()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('switchTo->alert->getText')->andReturn(
            'foo'
        );
        $browser = new Browser($driver);

        $browser->assertDialogOpened('foo');

        try {
            $browser->assertDialogOpened('Foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected dialog message [Foo] does not equal actual message [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_enabled()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->isEnabled')->andReturn(
            true,
            false
        );
        $browser = new Browser($driver);

        $browser->assertEnabled('foo');

        try {
            $browser->assertEnabled('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected element [foo] to be enabled, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_disabled()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement->isEnabled')->andReturn(
            false,
            true
        );
        $browser = new Browser($driver);

        $browser->assertDisabled('foo');

        try {
            $browser->assertDisabled('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected element [foo] to be disabled, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_focused()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->andReturn('element');
        $driver->shouldReceive('switchTo->activeElement->equals')->with('element')->andReturn(
            true,
            false
        );
        $browser = new Browser($driver);

        $browser->assertFocused('foo');

        try {
            $browser->assertFocused('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Expected element [foo] to be focused, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_focused()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->andReturn('element');
        $driver->shouldReceive('switchTo->activeElement->equals')->with('element')->andReturn(
            false,
            true
        );
        $browser = new Browser($driver);

        $browser->assertNotFocused('foo');

        try {
            $browser->assertNotFocused('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Expected element [foo] not to be focused, but it was.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(
            'bar'
        );
        $browser = new Browser($driver);

        $browser->assertVue('foo', 'bar', 'baz');

        try {
            $browser->assertVue('foo', 'Bar', 'baz');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Failed asserting that two strings are equal.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_is_not()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(
            'bar'
        );
        $browser = new Browser($driver);

        $browser->assertVueIsNot('foo', 'Bar', 'baz');

        try {
            $browser->assertVueIsNot('foo', 'bar', 'baz');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Failed asserting that \'bar\' is not equal to "bar".',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_contains()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(
            ['bar']
        );
        $browser = new Browser($driver);

        $browser->assertVueContains('foo', 'bar', 'baz');

        try {
            $browser->assertVueContains('foo', 'Bar', 'baz');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Failed asserting that an array contains 'Bar'.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_does_not_contain()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(
            ['bar']
        );
        $browser = new Browser($driver);

        $browser->assertVueDoesNotContain('foo', 'Bar', 'baz');

        try {
            $browser->assertVueDoesNotContain('foo', 'bar', 'baz');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Failed asserting that an array does not contain 'bar'.",
                $e->getMessage()
            );
        }
    }

    public function test_vue_attribute()
    {
        $driver = Mockery::mock(StdClass::class);
        $script = "return document.querySelector('body foo').__vue__.bar";
        $driver->shouldReceive('executeScript')->with($script)->andReturn(
            'baz'
        );
        $browser = new Browser($driver);

        $this->assertEquals('baz', $browser->vueAttribute('foo', 'bar'));
    }
}
