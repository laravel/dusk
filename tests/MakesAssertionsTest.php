<?php

namespace Laravel\Dusk\Tests;

use stdClass;
use Mockery as m;
use Laravel\Dusk\Browser;
use PHPUnit\Framework\TestCase;
use Facebook\WebDriver\Remote\RemoteWebElement;
use PHPUnit\Framework\ExpectationFailedException;

class MakesAssertionsTest extends TestCase
{
    public function test_assert_title()
    {
        $driver = m::mock(stdClass::class);
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
        $driver = m::mock(stdClass::class);
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

    public function test_assert_present()
    {
        $driver = m::mock(stdClass::class);
        $element = m::mock(stdClass::class);
        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('find')->with('foo')->andReturn(
            $element,
            null
        );
        $browser = new Browser($driver, $resolver);

        $browser->assertPresent('foo');

        try {
            $browser->assertPresent('foo');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                "Element [body foo] is not present.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_enabled()
    {
        $driver = m::mock(stdClass::class);
        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForField->isEnabled')->andReturn(
            true,
            false
        );
        $browser = new Browser($driver, $resolver);

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
        $driver = m::mock(stdClass::class);
        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForField->isEnabled')->andReturn(
            false,
            true
        );
        $browser = new Browser($driver, $resolver);

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
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('switchTo->activeElement->equals')->with('element')->andReturn(
            true,
            false
        );
        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForField')->with('foo')->andReturn('element');
        $browser = new Browser($driver, $resolver);

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
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('switchTo->activeElement->equals')->with('element')->andReturn(
            false,
            true
        );
        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForField')->with('foo')->andReturn('element');
        $browser = new Browser($driver, $resolver);

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

    public function test_assert_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->with('select[name="users"]', [2])
            ->andReturn([$element]);

        $browser = new Browser($driver, $resolver);

        $browser->assertSelected('select[name="users"]', 2);

        try {
            $browser->assertNotSelected('select[name="users"]', 2);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertContains(
                'Unexpected value [2] selected for [select[name="users"]].',
                $e->getMessage()
            );
        }
    }
}
