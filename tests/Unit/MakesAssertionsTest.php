<?php

namespace Laravel\Dusk\Tests\Unit;

use Facebook\WebDriver\Remote\RemoteWebElement;
use Laravel\Dusk\Browser;
use Mockery as m;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use stdClass;

class MakesAssertionsTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

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
            $this->assertStringContainsString(
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
            $this->assertStringContainsString(
                'Did not see expected text [Fo] within title [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_checked_and_element_is_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForChecking')
            ->with('input[type="checkbox"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertChecked('input[type="checkbox"]', 1);
    }

    public function test_assert_checked_and_element_is_not_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForChecking')
            ->with('input[type="checkbox"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertChecked('input[type="checkbox"]', 1);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Expected checkbox [input[type="checkbox"]] to be checked, but it wasn\'t.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_checked_and_element_is_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForChecking')
            ->with('input[type="checkbox"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertNotChecked('input[type="checkbox"]', 1);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Checkbox [input[type="checkbox"]] was unexpectedly checked.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_checked_and_element_is_not_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForChecking')
            ->with('input[type="checkbox"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertNotChecked('input[type="checkbox"]', 1);
    }

    public function test_assert_radio_selected_and_element_is_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForRadioSelection')
            ->with('input[type="radio"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertRadioSelected('input[type="radio"]', 1);
    }

    public function test_assert_radio_selected_and_element_is_not_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForRadioSelection')
            ->with('input[type="radio"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertRadioSelected('input[type="radio"]', 1);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Expected radio [input[type="radio"]] to be selected, but it wasn\'t.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_radio_not_selected_and_element_is_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForRadioSelection')
            ->with('input[type="radio"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertRadioNotSelected('input[type="radio"]', 1);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Radio [input[type="radio"]] was unexpectedly selected.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_radio_not_selected_and_element_is_not_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForRadioSelection')
            ->with('input[type="radio"]', 1)
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertRadioNotSelected('input[type="radio"]', 1);
    }

    public function test_assert_selected_and_element_is_selected()
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
    }

    public function test_assert_selected_and_element_is_not_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->with('select[name="users"]', [2])
            ->andReturn([$element]);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertSelected('select[name="users"]', 2);
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Expected value [2] to be selected for [select[name="users"]], but it wasn\'t.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_selected_and_element_is_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->with('select[name="users"]', [2])
            ->andReturn([$element]);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertNotSelected('select[name="users"]', 2);
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Unexpected value [2] selected for [select[name="users"]].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_selected_and_element_is_not_selected()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('isSelected')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->with('select[name="users"]', [2])
            ->andReturn([$element]);

        $browser = new Browser($driver, $resolver);

        $browser->assertNotSelected('select[name="users"]', 2);
    }

    public function test_assert_select_has_options_and_option_exists()
    {
        $driver = m::mock(stdClass::class);

        $option = m::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')
            ->andReturn(1);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([$option]);

        $browser = new Browser($driver, $resolver);

        $browser->assertSelectHasOptions('select[name="users"]', [1]);
    }

    public function test_assert_select_has_options_and_option_empty()
    {
        $driver = m::mock(stdClass::class);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([]);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertSelectHasOptions('select[name="users"]', [1]);
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Expected options [1] for selection field [select[name="users"]] to be available.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_missing_options_and_option_exists()
    {
        $driver = m::mock(stdClass::class);

        $option = m::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')
            ->andReturn(1);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([$option]);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertSelectMissingOptions('select[name="users"]', [2]);
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Unexpected options [2] for selection field [select[name="users"]].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_missing_options_and_option_empty()
    {
        $driver = m::mock(stdClass::class);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([]);

        $browser = new Browser($driver, $resolver);

        $browser->assertSelectMissingOptions('select[name="users"]', [1]);
    }

    public function test_assert_select_has_option_and_option_exists()
    {
        $driver = m::mock(stdClass::class);

        $option = m::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')
            ->andReturn(1);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([$option]);

        $browser = new Browser($driver, $resolver);

        $browser->assertSelectHasOption('select[name="users"]', 1);
    }

    public function test_assert_select_has_option_and_option_empty()
    {
        $driver = m::mock(stdClass::class);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([]);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertSelectHasOption('select[name="users"]', 1);
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Expected options [1] for selection field [select[name="users"]] to be available.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_missing_option_and_option_exists()
    {
        $driver = m::mock(stdClass::class);

        $option = m::mock(RemoteWebElement::class);
        $option->shouldReceive('getAttribute')
            ->andReturn(1);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([$option]);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertSelectMissingOption('select[name="users"]', 2);
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Unexpected options [2] for selection field [select[name="users"]].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_select_missing_option_and_option_empty()
    {
        $driver = m::mock(stdClass::class);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveSelectOptions')
            ->andReturn([]);

        $browser = new Browser($driver, $resolver);

        $browser->assertSelectMissingOption('select[name="users"]', 1);
    }

    public function test_assert_value_using_supported_element()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getTagName')->andReturn('textarea');
        $element->shouldReceive('getAttribute')->andReturn('bar');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertValue('foo', 'bar');

        try {
            $browser->assertValue('foo', 'foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected value [foo] within element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_value_is_not_using_supported_element()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getTagName')->andReturn('meter');
        $element->shouldReceive('getAttribute')->andReturn('bar');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertValueIsNot('foo', 'foo');

        try {
            $browser->assertValueIsNot('foo', 'bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected value [bar] within element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_value_using_unsupported_element()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getTagName')->andReturn('p');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertValue('foo', 'bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'This assertion cannot be used with the element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_value_is_not_using_unsupported_element()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(RemoteWebElement::class);
        $element->shouldReceive('getTagName')->andReturn('div');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertValueIsNot('foo', 'foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'This assertion cannot be used with the element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_attribute()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getAttribute')->with('bar')->andReturn(
            'joe',
            null,
            'sue'
        );

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('Foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertAttribute('foo', 'bar', 'joe');

        try {
            $browser->assertAttribute('foo', 'bar', 'joe');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected attribute [bar] within element [Foo].',
                $e->getMessage()
            );
        }

        try {
            $browser->assertAttribute('foo', 'bar', 'joe');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                "Expected 'bar' attribute [joe] does not equal actual value [sue].",
                $e->getMessage()
            );
        }
    }

    public function test_assert_attribute_missing()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getAttribute')->with('bar')->andReturn(
            null,
            'joe',
        );

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('Foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertAttributeMissing('foo', 'bar');

        try {
            $browser->assertAttributeMissing('foo', 'bar');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected attribute [bar] within element [Foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_attribute_contains()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getAttribute')->with('bar')->andReturn(
            'class-a class-b',
            null,
            'class-1 class-2'
        );

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('Foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertAttributeContains('foo', 'bar', 'class-b');

        try {
            $browser->assertAttributeContains('foo', 'bar', 'class-b');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected attribute [bar] within element [Foo].',
                $e->getMessage()
            );
        }

        try {
            $browser->assertAttributeContains('foo', 'bar', 'class-b');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                "Attribute 'bar' does not contain [class-b]. Full attribute value was [class-1 class-2].",
                $e->getMessage()
            );
        }
    }

    public function test_assert_attribute_does_not_contain()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getAttribute')->with('bar')->andReturn(
            'class-a class-b',
            null,
            'class-1 class-2'
        );

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('Foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertAttributeDoesntContain('foo', 'bar', 'class-c');

        try {
            $browser->assertAttributeDoesntContain('foo', 'bar', 'class-c');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected attribute [bar] within element [Foo].',
                $e->getMessage()
            );
        }

        try {
            $browser->assertAttributeDoesntContain('foo', 'bar', 'class-1');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                "Attribute 'bar' contains [class-1]. Full attribute value was [class-1 class-2].",
                $e->getMessage()
            );
        }
    }

    public function test_assert_data_attribute()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getAttribute')->with('data-bar')->andReturn(
            'joe',
            null,
            'sue'
        );

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('Foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertDataAttribute('foo', 'bar', 'joe');

        try {
            $browser->assertDataAttribute('foo', 'bar', 'joe');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected attribute [data-bar] within element [Foo].',
                $e->getMessage()
            );
        }

        try {
            $browser->assertDataAttribute('foo', 'bar', 'joe');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                "Expected 'data-bar' attribute [joe] does not equal actual value [sue].",
                $e->getMessage()
            );
        }
    }

    public function test_assert_aria_attribute()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getAttribute')->with('aria-bar')->andReturn(
            'joe',
            null,
            'sue'
        );

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('Foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertAriaAttribute('foo', 'bar', 'joe');

        try {
            $browser->assertAriaAttribute('foo', 'bar', 'joe');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected attribute [aria-bar] within element [Foo].',
                $e->getMessage()
            );
        }

        try {
            $browser->assertAriaAttribute('foo', 'bar', 'joe');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                "Expected 'aria-bar' attribute [joe] does not equal actual value [sue].",
                $e->getMessage()
            );
        }
    }

    public function test_assert_visible_and_element_is_displayed()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('isDisplayed')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertVisible('foo');
    }

    public function test_assert_visible_and_element_is_not_displayed()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('isDisplayed')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertVisible('foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Element [body foo] is not visible.',
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
            $this->assertStringContainsString(
                'Element [body foo] is not present.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_not_present()
    {
        $driver = m::mock(stdClass::class);
        $element = m::mock(stdClass::class);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('find')->with('foo')->andReturn(
            null,
            null
        );
        $resolver->shouldReceive('format')->with('bar')->andReturn('body bar');
        $resolver->shouldReceive('find')->with('bar')->andReturn(
            $element,
            null
        );

        $browser = new Browser($driver, $resolver);

        $browser->assertNotPresent('foo');

        try {
            $browser->assertNotPresent('bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Element [body bar] is present.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_missing_and_element_is_displayed()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('isDisplayed')->andReturn(true);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertMissing('foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_missing_and_element_is_not_displayed()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('isDisplayed')->andReturn(false);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertMissing('foo');
    }

    public function test_assert_dialog_opened()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('switchTo->alert->getText')->andReturn('foo');

        $resolver = m::mock(stdClass::class);

        $browser = new Browser($driver, $resolver);

        $browser->assertDialogOpened('foo');

        try {
            $browser->assertDialogOpened('bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Expected dialog message [bar] does not equal actual message [foo].',
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
            $this->assertStringContainsString(
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
            $this->assertStringContainsString(
                "Expected element [foo] to be disabled, but it wasn't.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_button_enabled()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("Expected button [Cant press me] to be enabled, but it wasn't.");

        $driver = m::mock(stdClass::class);
        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForButtonPress->isEnabled')->andReturn(
            true,
            false
        );

        $browser = new Browser($driver, $resolver);

        $browser->assertButtonEnabled('Press me');

        $browser->assertButtonEnabled('Cant press me');
    }

    public function test_assert_button_disabled()
    {
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage("Expected button [Press me] to be disabled, but it wasn't.");

        $driver = m::mock(stdClass::class);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForButtonPress->isEnabled')->twice()->andReturn(
            false,
            true
        );

        $browser = new Browser($driver, $resolver);

        $browser->assertButtonDisabled('Cant press me');

        $browser->assertButtonDisabled('Press me');
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
            $this->assertStringContainsString(
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
            $this->assertStringContainsString(
                'Expected element [foo] not to be focused, but it was.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->with(
                "var el = document.querySelector('body foo');".
                "if (typeof el.__vue__ !== 'undefined')".
                '    return el.__vue__.foo;'.
                'try {'.
                '    var attr = el.__vueParentComponent.ctx.foo;'.
                "    if (typeof attr !== 'undefined')".
                '        return attr;'.
                '} catch (e) {}'.
                'return el.__vueParentComponent.setupState.foo;'
            )
            ->twice()
            ->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->assertVue('foo', 'foo', 'foo');

        try {
            $browser->assertVue('foo', 'bar', 'foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected value ["bar"] at the key [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_with_array()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(['john', 'jane']);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('@vue-component')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->assertVue('users', ['john', 'jane'], '@vue-component');

        try {
            $browser->assertVue('users', ['john'], '@vue-component');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected value [["john"]] at the key [users].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_is_not()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->with(
                "var el = document.querySelector('body foo');".
                "if (typeof el.__vue__ !== 'undefined')".
                '    return el.__vue__.foo;'.
                'try {'.
                '    var attr = el.__vueParentComponent.ctx.foo;'.
                "    if (typeof attr !== 'undefined')".
                '        return attr;'.
                '} catch (e) {}'.
                'return el.__vueParentComponent.setupState.foo;'
            )
            ->twice()
            ->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->assertVueIsNot('foo', 'bar', 'foo');

        try {
            $browser->assertVueIsNot('foo', 'foo', 'foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected value ["foo"] at the key [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_is_not_with_array()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(['john', 'jane']);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('@vue-component')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->assertVueIsNot('users', ['jane', 'john'], '@vue-component');

        try {
            $browser->assertVueIsNot('users', ['john', 'jane'], '@vue-component');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected value [["john","jane"]] at the key [users].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_contains_formats_vue_prop_query()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')
            ->with(
                'var el = document.querySelector(\'body [dusk="vue-component"]\');'.
                "if (typeof el.__vue__ !== 'undefined')".
                '    return el.__vue__.name;'.
                'try {'.
                '    var attr = el.__vueParentComponent.ctx.name;'.
                "    if (typeof attr !== 'undefined')".
                '        return attr;'.
                '} catch (e) {}'.
                'return el.__vueParentComponent.setupState.name;'
            )
            ->once()
            ->andReturn(['john']);

        $browser = new Browser($driver);

        $browser->assertVueContains('name', 'john', '@vue-component');
    }

    public function test_assert_vue_contains()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(['john']);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('@vue-component')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        $browser->assertVueContains('users', 'john', '@vue-component');

        try {
            $browser->assertVueDoesNotContain('users', 'john', '@vue-component');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                "Failed asserting that an array does not contain 'john'.",
                $e->getMessage()
            );
        }
    }

    public function test_assert_vue_contains_with_no_result()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->andReturn(null);

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('@vue-component')->andReturn('body foo');

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertVueContains('users', 'john', '@vue-component');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'The attribute for key [users] is not an array.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_script()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('executeScript')->withArgs(['return 1==1'])->andReturn(true);
        $driver->shouldReceive('executeScript')->withArgs(['return 1==1'])->andReturn(true);
        $driver->shouldReceive('executeScript')->withArgs(['return 1==2'])->andReturn(false);
        $driver->shouldReceive('executeScript')->withArgs(["return 'some string'"])->andReturn('some string');

        $resolver = m::mock(stdClass::class);

        $browser = new Browser($driver, $resolver);

        $browser->assertScript('return 1==1');
        $browser->assertScript('1==1');
        $browser->assertScript("'some string'", 'some string');

        try {
            $browser->assertScript('1==2');
            $this->fail();
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'JavaScript expression [return 1==2] mismatched.',
                $e->getMessage()
            );
            $this->assertStringContainsString(
                'Failed asserting that false matches expected true.',
                $e->getMessage()
            );
        }
    }

    public function test_assert_see()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('')->andReturn('body');
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertSee('foo');

        try {
            $browser->assertSee('bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected text [bar] within element [body].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dont_see()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('')->andReturn('body');
        $resolver->shouldReceive('findOrFail')->with('')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertDontSee('bar');

        try {
            $browser->assertDontSee('foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected text [foo] within element [body].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_see_in()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertSeeIn('foo', 'foo');

        try {
            $browser->assertSeeIn('foo', 'bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected text [bar] within element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dont_see_in()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertDontSeeIn('foo', 'bar');

        try {
            $browser->assertDontSeeIn('foo', 'foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected text [foo] within element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_see_empty_text_in_element_with_empty_text()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertSeeNothingIn('foo');
    }

    public function test_assert_see_empty_text_in_element_without_empty_text()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertSeeNothingIn('foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not see expected text [\'\'] within element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dont_see_empty_text_in_element_with_empty_text()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        try {
            $browser->assertSeeAnythingIn('foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Saw unexpected text [\'\'] within element [body foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_dont_see_empty_text_in_element_without_empty_text()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getText')->andReturn('foo');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('format')->with('foo')->andReturn('body foo');
        $resolver->shouldReceive('findOrFail')->with('foo')->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertSeeAnythingIn('foo');
    }

    public function test_assert_source_has()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getPageSource')->andReturn('foo');

        $resolver = m::mock(stdClass::class);

        $browser = new Browser($driver, $resolver);

        $browser->assertSourceHas('foo');

        try {
            $browser->assertSourceHas('bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Did not find expected source code [bar].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_source_missing()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('getPageSource')->andReturn('foo');

        $resolver = m::mock(stdClass::class);

        $browser = new Browser($driver, $resolver);

        $browser->assertSourceMissing('bar');

        try {
            $browser->assertSourceMissing('foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Found unexpected source code [foo].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_input_value()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getTagName')->andReturn('input');
        $element->shouldReceive('getAttribute')->andReturn('bar');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForTyping')
            ->with('foo')
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertInputValue('foo', 'bar');

        try {
            $browser->assertInputValue('foo', 'foo');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Expected value [foo] for the [foo] input does not equal the actual value [bar].',
                $e->getMessage()
            );
        }
    }

    public function test_assert_input_value_is_not()
    {
        $driver = m::mock(stdClass::class);

        $element = m::mock(stdClass::class);
        $element->shouldReceive('getTagName')->andReturn('input');
        $element->shouldReceive('getAttribute')->andReturn('bar');

        $resolver = m::mock(stdClass::class);
        $resolver->shouldReceive('resolveForTyping')
            ->with('foo')
            ->andReturn($element);

        $browser = new Browser($driver, $resolver);

        $browser->assertInputValueIsNot('foo', 'foo');

        try {
            $browser->assertInputValueIsNot('foo', 'bar');
        } catch (ExpectationFailedException $e) {
            $this->assertStringContainsString(
                'Value [bar] for the [foo] input should not equal the actual value.',
                $e->getMessage()
            );
        }
    }
}
