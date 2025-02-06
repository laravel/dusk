<?php

namespace Laravel\Dusk\Concerns;

use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;

trait MakesAssertions
{
    /**
     * Indicates the browser has made an assertion about the source code of the page.
     *
     * @var bool
     */
    public $madeSourceAssertion = false;

    /**
     * Assert that the page title matches the given text.
     *
     * @param  string  $title
     * @return $this
     */
    public function assertTitle($title)
    {
        PHPUnit::assertEquals(
            $title, $this->driver->getTitle(),
            "Expected title [{$title}] does not equal actual title [{$this->driver->getTitle()}]."
        );

        return $this;
    }

    /**
     * Assert that the page title contains the given text.
     *
     * @param  string  $title
     * @return $this
     */
    public function assertTitleContains($title)
    {
        PHPUnit::assertTrue(
            Str::contains($this->driver->getTitle(), $title),
            "Did not see expected text [{$title}] within title [{$this->driver->getTitle()}]."
        );

        return $this;
    }

    /**
     * Assert that the given encrypted cookie is present.
     *
     * @param  string  $name
     * @param  bool  $decrypt
     * @return $this
     */
    public function assertHasCookie($name, $decrypt = true)
    {
        $cookie = $decrypt ? $this->cookie($name) : $this->plainCookie($name);

        PHPUnit::assertTrue(
            ! is_null($cookie),
            "Did not find expected cookie [{$name}]."
        );

        return $this;
    }

    /**
     * Assert that the given unencrypted cookie is present.
     *
     * @param  string  $name
     * @return $this
     */
    public function assertHasPlainCookie($name)
    {
        return $this->assertHasCookie($name, false);
    }

    /**
     * Assert that the given encrypted cookie is not present.
     *
     * @param  string  $name
     * @param  bool  $decrypt
     * @return $this
     */
    public function assertCookieMissing($name, $decrypt = true)
    {
        $cookie = $decrypt ? $this->cookie($name) : $this->plainCookie($name);

        PHPUnit::assertTrue(
            is_null($cookie),
            "Found unexpected cookie [{$name}]."
        );

        return $this;
    }

    /**
     * Assert that the given unencrypted cookie is not present.
     *
     * @param  string  $name
     * @return $this
     */
    public function assertPlainCookieMissing($name)
    {
        return $this->assertCookieMissing($name, false);
    }

    /**
     * Assert that an encrypted cookie has a given value.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  bool  $decrypt
     * @return $this
     */
    public function assertCookieValue($name, $value, $decrypt = true)
    {
        $actual = $decrypt ? $this->cookie($name) : $this->plainCookie($name);

        PHPUnit::assertEquals(
            $value,
            $actual,
            "Cookie [{$name}] had value [{$actual}], but expected [{$value}]."
        );

        return $this;
    }

    /**
     * Assert that an unencrypted cookie has a given value.
     *
     * @param  string  $name
     * @param  string  $value
     * @return $this
     */
    public function assertPlainCookieValue($name, $value)
    {
        return $this->assertCookieValue($name, $value, false);
    }

    /**
     * Assert that the given text is present on the page.
     *
     * @param  string  $text
     * @param  bool  $ignoreCase
     * @return $this
     */
    public function assertSee($text, $ignoreCase = false)
    {
        return $this->assertSeeIn('', $text, $ignoreCase);
    }

    /**
     * Assert that the given text is not present on the page.
     *
     * @param  string  $text
     * @param  bool  $ignoreCase
     * @return $this
     */
    public function assertDontSee($text, $ignoreCase = false)
    {
        return $this->assertDontSeeIn('', $text, $ignoreCase);
    }

    /**
     * Assert that the given text is present within the selector.
     *
     * @param  string  $selector
     * @param  string  $text
     * @param  bool  $ignoreCase
     * @return $this
     */
    public function assertSeeIn($selector, $text, $ignoreCase = false)
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertTrue(
            Str::contains($element->getText(), $text, $ignoreCase),
            "Did not see expected text [{$text}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that the given text is not present within the selector.
     *
     * @param  string  $selector
     * @param  string  $text
     * @param  bool  $ignoreCase
     * @return $this
     */
    public function assertDontSeeIn($selector, $text, $ignoreCase = false)
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertFalse(
            Str::contains($element->getText(), $text, $ignoreCase),
            "Saw unexpected text [{$text}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that any text is present within the selector.
     *
     * @param  string  $selector
     * @return $this
     */
    public function assertSeeAnythingIn($selector)
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertTrue(
            $element->getText() !== '',
            "Saw unexpected text [''] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that no text is present within the selector.
     *
     * @param  string  $selector
     * @return $this
     */
    public function assertSeeNothingIn($selector)
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertTrue(
            $element->getText() === '',
            "Did not see expected text [''] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that a given element is present a given amount of times.
     *
     * @param  string  $selector
     * @param  int  $expected
     * @return $this
     */
    public function assertCount($selector, $expected)
    {
        $fullSelector = $this->resolver->format($selector);

        PHPUnit::assertCount(
            $expected,
            $this->resolver->all($selector),
            "Expected element [{$fullSelector}] exactly {$expected} times."
        );

        return $this;
    }

    /**
     * Assert that the given JavaScript expression evaluates to the given value.
     *
     * @param  string  $expression
     * @param  mixed  $expected
     * @return $this
     */
    public function assertScript($expression, $expected = true)
    {
        $expression = Str::start($expression, 'return ');

        PHPUnit::assertEquals(
            $expected,
            $this->driver->executeScript($expression),
            "JavaScript expression [{$expression}] mismatched."
        );

        return $this;
    }

    /**
     * Assert that the given source code is present on the page.
     *
     * @param  string  $code
     * @return $this
     */
    public function assertSourceHas($code)
    {
        $this->madeSourceAssertion = true;

        PHPUnit::assertTrue(
            Str::contains($this->driver->getPageSource(), $code),
            "Did not find expected source code [{$code}]."
        );

        return $this;
    }

    /**
     * Assert that the given source code is not present on the page.
     *
     * @param  string  $code
     * @return $this
     */
    public function assertSourceMissing($code)
    {
        $this->madeSourceAssertion = true;

        PHPUnit::assertFalse(
            Str::contains($this->driver->getPageSource(), $code),
            "Found unexpected source code [{$code}]."
        );

        return $this;
    }

    /**
     * Assert that the given link is present on the page.
     *
     * @param  string  $link
     * @return $this
     */
    public function assertSeeLink($link)
    {
        if ($this->resolver->prefix) {
            $message = "Did not see expected link [{$link}] within [{$this->resolver->prefix}].";
        } else {
            $message = "Did not see expected link [{$link}].";
        }

        PHPUnit::assertTrue(
            $this->seeLink($link),
            $message
        );

        return $this;
    }

    /**
     * Assert that the given link is not present on the page.
     *
     * @param  string  $link
     * @return $this
     */
    public function assertDontSeeLink($link)
    {
        if ($this->resolver->prefix) {
            $message = "Saw unexpected link [{$link}] within [{$this->resolver->prefix}].";
        } else {
            $message = "Saw unexpected link [{$link}].";
        }

        PHPUnit::assertFalse(
            $this->seeLink($link),
            $message
        );

        return $this;
    }

    /**
     * Determine if the given link is visible.
     *
     * @param  string  $link
     * @return bool
     */
    public function seeLink($link)
    {
        $this->ensurejQueryIsAvailable();

        $selector = addslashes(trim($this->resolver->format('a')));

        $link = str_replace("'", "\\\\'", $link);

        $script = <<<JS
            var link = jQuery.find(`{$selector}:contains('{$link}')`);
            return link.length > 0 && jQuery(link).is(':visible');
JS;

        return $this->driver->executeScript($script);
    }

    /**
     * Assert that the given input field has the given value.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertInputValue($field, $value)
    {
        PHPUnit::assertEquals(
            $value,
            $this->inputValue($field),
            "Expected value [{$value}] for the [{$field}] input does not equal the actual value [{$this->inputValue($field)}]."
        );

        return $this;
    }

    /**
     * Assert that the given input field does not have the given value.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertInputValueIsNot($field, $value)
    {
        PHPUnit::assertNotEquals(
            $value,
            $this->inputValue($field),
            "Value [{$value}] for the [{$field}] input should not equal the actual value."
        );

        return $this;
    }

    /**
     * Get the value of the given input or text area field.
     *
     * @param  string  $field
     * @return string
     */
    public function inputValue($field)
    {
        $element = $this->resolver->resolveForTyping($field);

        return in_array($element->getTagName(), ['input', 'textarea'])
                        ? $element->getAttribute('value')
                        : $element->getText();
    }

    /**
     * Assert that the given input field is present.
     *
     * @param  string  $field
     * @return $this
     */
    public function assertInputPresent($field)
    {
        $this->assertPresent(
            "input[name='{$field}'], textarea[name='{$field}'], select[name='{$field}']"
        );

        return $this;
    }

    /**
     * Assert that the given input field is not visible.
     *
     * @param  string  $field
     * @return $this
     */
    public function assertInputMissing($field)
    {
        $this->assertMissing(
            "input[name='{$field}'], textarea[name='{$field}'], select[name='{$field}']"
        );

        return $this;
    }

    /**
     * Assert that the given checkbox is checked.
     *
     * @param  string  $field
     * @param  string|null  $value
     * @return $this
     */
    public function assertChecked($field, $value = null)
    {
        $element = $this->resolver->resolveForChecking($field, $value);

        PHPUnit::assertTrue(
            $element->isSelected(),
            "Expected checkbox [{$field}] to be checked, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given checkbox is not checked.
     *
     * @param  string  $field
     * @param  string|null  $value
     * @return $this
     */
    public function assertNotChecked($field, $value = null)
    {
        $element = $this->resolver->resolveForChecking($field, $value);

        PHPUnit::assertFalse(
            $element->isSelected(),
            "Checkbox [{$field}] was unexpectedly checked."
        );

        return $this;
    }

    /**
     * Assert that the given checkbox is in an indeterminate state.
     *
     * @param  string  $field
     * @param  string|null  $value
     * @return $this
     */
    public function assertIndeterminate($field, $value = null)
    {
        $this->assertNotChecked($field, $value);

        PHPUnit::assertSame(
            'true',
            $this->resolver->findOrFail($field)->getAttribute('indeterminate'),
            "Checkbox [{$field}] was not in indeterminate state."
        );

        return $this;
    }

    /**
     * Assert that the given radio field is selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertRadioSelected($field, $value)
    {
        $element = $this->resolver->resolveForRadioSelection($field, $value);

        PHPUnit::assertTrue(
            $element->isSelected(),
            "Expected radio [{$field}] to be selected, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given radio field is not selected.
     *
     * @param  string  $field
     * @param  string|null  $value
     * @return $this
     */
    public function assertRadioNotSelected($field, $value = null)
    {
        $element = $this->resolver->resolveForRadioSelection($field, $value);

        PHPUnit::assertFalse(
            $element->isSelected(),
            "Radio [{$field}] was unexpectedly selected."
        );

        return $this;
    }

    /**
     * Assert that the given dropdown has the given value selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertSelected($field, $value)
    {
        PHPUnit::assertTrue(
            $this->selected($field, $value),
            "Expected value [{$value}] to be selected for [{$field}], but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given dropdown does not have the given value selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertNotSelected($field, $value)
    {
        PHPUnit::assertFalse(
            $this->selected($field, $value),
            "Unexpected value [{$value}] selected for [{$field}]."
        );

        return $this;
    }

    /**
     * Assert that the given array of values are available to be selected.
     *
     * @param  string  $field
     * @param  array  $values
     * @return $this
     */
    public function assertSelectHasOptions($field, array $values)
    {
        $options = $this->resolver->resolveSelectOptions($field, $values);

        $options = collect($options)->unique(function (RemoteWebElement $option) {
            return $option->getAttribute('value');
        })->all();

        PHPUnit::assertCount(
            count($values),
            $options,
            'Expected options ['.implode(',', $values)."] for selection field [{$field}] to be available."
        );

        return $this;
    }

    /**
     * Assert that the given array of values are not available to be selected.
     *
     * @param  string  $field
     * @param  array  $values
     * @return $this
     */
    public function assertSelectMissingOptions($field, array $values)
    {
        PHPUnit::assertCount(
            0,
            $this->resolver->resolveSelectOptions($field, $values),
            'Unexpected options ['.implode(',', $values)."] for selection field [{$field}]."
        );

        return $this;
    }

    /**
     * Assert that the given value is available to be selected on the given field.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertSelectHasOption($field, $value)
    {
        return $this->assertSelectHasOptions($field, [$value]);
    }

    /**
     * Assert that the given value is not available to be selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertSelectMissingOption($field, $value)
    {
        return $this->assertSelectMissingOptions($field, [$value]);
    }

    /**
     * Determine if the given value is selected for the given select field.
     *
     * @param  string  $field
     * @param  string  $value
     * @return bool
     */
    public function selected($field, $value)
    {
        $options = $this->resolver->resolveSelectOptions($field, (array) $value);

        return collect($options)->contains(function (RemoteWebElement $option) {
            return $option->isSelected();
        });
    }

    /**
     * Assert that the element matching the given selector has the given value.
     *
     * @param  string  $selector
     * @param  string  $value
     * @return $this
     */
    public function assertValue($selector, $value)
    {
        $fullSelector = $this->resolver->format($selector);

        $this->ensureElementSupportsValueAttribute(
            $element = $this->resolver->findOrFail($selector),
            $fullSelector
        );

        $actual = $element->getAttribute('value');

        PHPUnit::assertEquals(
            $value,
            $actual,
            "Did not see expected value [{$value}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector does not have the given value.
     *
     * @param  string  $selector
     * @param  string  $value
     * @return $this
     */
    public function assertValueIsNot($selector, $value)
    {
        $fullSelector = $this->resolver->format($selector);

        $this->ensureElementSupportsValueAttribute(
            $element = $this->resolver->findOrFail($selector),
            $fullSelector
        );

        $actual = $element->getAttribute('value');

        PHPUnit::assertNotEquals(
            $value,
            $actual,
            "Saw unexpected value [{$value}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Ensure the given element supports the 'value' attribute.
     *
     * @param  mixed  $element
     * @param  string  $fullSelector
     * @return void
     */
    public function ensureElementSupportsValueAttribute($element, $fullSelector)
    {
        PHPUnit::assertTrue(in_array($element->getTagName(), [
            'textarea',
            'select',
            'button',
            'input',
            'li',
            'meter',
            'option',
            'param',
            'progress',
        ]), "This assertion cannot be used with the element [{$fullSelector}].");
    }

    /**
     * Assert that the element matching the given selector has the given value in the provided attribute.
     *
     * @param  string  $selector
     * @param  string  $attribute
     * @param  string  $value
     * @return $this
     */
    public function assertAttribute($selector, $attribute, $value)
    {
        $fullSelector = $this->resolver->format($selector);

        $actual = $this->resolver->findOrFail($selector)->getAttribute($attribute);

        PHPUnit::assertNotNull(
            $actual,
            "Did not see expected attribute [{$attribute}] within element [{$fullSelector}]."
        );

        PHPUnit::assertEquals(
            $value,
            $actual,
            "Expected '$attribute' attribute [{$value}] does not equal actual value [$actual]."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector is missing the provided attribute.
     *
     * @param  string  $selector
     * @param  string  $attribute
     * @return $this
     */
    public function assertAttributeMissing($selector, $attribute)
    {
        $fullSelector = $this->resolver->format($selector);

        $actual = $this->resolver->findOrFail($selector)->getAttribute($attribute);

        PHPUnit::assertNull(
            $actual,
            "Saw unexpected attribute [{$attribute}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector contains the given value in the provided attribute.
     *
     * @param  string  $selector
     * @param  string  $attribute
     * @param  string  $value
     * @return $this
     */
    public function assertAttributeContains($selector, $attribute, $value)
    {
        $fullSelector = $this->resolver->format($selector);

        $actual = $this->resolver->findOrFail($selector)->getAttribute($attribute);

        PHPUnit::assertNotNull(
            $actual,
            "Did not see expected attribute [{$attribute}] within element [{$fullSelector}]."
        );

        PHPUnit::assertStringContainsString(
            $value,
            $actual,
            "Attribute '$attribute' does not contain [{$value}]. Full attribute value was [$actual]."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector does not contain the given value in the provided attribute.
     *
     * @param  string  $selector
     * @param  string  $attribute
     * @param  string  $value
     * @return $this
     */
    public function assertAttributeDoesntContain($selector, $attribute, $value)
    {
        $actual = $this->resolver->findOrFail($selector)->getAttribute($attribute);

        if (is_null($actual)) {
            return $this;
        }

        PHPUnit::assertStringNotContainsString(
            $value,
            $actual,
            "Attribute '$attribute' contains [{$value}]. Full attribute value was [$actual]."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector has the given value in the provided aria attribute.
     *
     * @param  string  $selector
     * @param  string  $attribute
     * @param  string  $value
     * @return $this
     */
    public function assertAriaAttribute($selector, $attribute, $value)
    {
        return $this->assertAttribute($selector, 'aria-'.$attribute, $value);
    }

    /**
     * Assert that the element matching the given selector has the given value in the provided data attribute.
     *
     * @param  string  $selector
     * @param  string  $attribute
     * @param  string  $value
     * @return $this
     */
    public function assertDataAttribute($selector, $attribute, $value)
    {
        return $this->assertAttribute($selector, 'data-'.$attribute, $value);
    }

    /**
     * Assert that the element matching the given selector is visible.
     *
     * @param  string  $selector
     * @return $this
     */
    public function assertVisible($selector)
    {
        $fullSelector = $this->resolver->format($selector);

        PHPUnit::assertTrue(
            $this->resolver->findOrFail($selector)->isDisplayed(),
            "Element [{$fullSelector}] is not visible."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector is present.
     *
     * @param  string  $selector
     * @return $this
     */
    public function assertPresent($selector)
    {
        $fullSelector = $this->resolver->format($selector);

        PHPUnit::assertTrue(
            ! is_null($this->resolver->find($selector)),
            "Element [{$fullSelector}] is not present."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector is not present in the source.
     *
     * @param  string  $selector
     * @return $this
     */
    public function assertNotPresent($selector)
    {
        $fullSelector = $this->resolver->format($selector);

        PHPUnit::assertTrue(
            is_null($this->resolver->find($selector)),
            "Element [{$fullSelector}] is present."
        );

        return $this;
    }

    /**
     * Assert that the element matching the given selector is not visible.
     *
     * @param  string  $selector
     * @return $this
     */
    public function assertMissing($selector)
    {
        $fullSelector = $this->resolver->format($selector);

        try {
            $missing = ! $this->resolver->findOrFail($selector)->isDisplayed();
        } catch (NoSuchElementException $e) {
            $missing = true;
        }

        PHPUnit::assertTrue(
            $missing,
            "Saw unexpected element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that a JavaScript dialog with the given message has been opened.
     *
     * @param  string  $message
     * @return $this
     */
    public function assertDialogOpened($message)
    {
        $actualMessage = $this->driver->switchTo()->alert()->getText();

        PHPUnit::assertEquals(
            $message,
            $actualMessage,
            "Expected dialog message [{$message}] does not equal actual message [{$actualMessage}]."
        );

        return $this;
    }

    /**
     * Assert that the given field is enabled.
     *
     * @param  string  $field
     * @return $this
     */
    public function assertEnabled($field)
    {
        $element = $this->resolver->resolveForField($field);

        PHPUnit::assertTrue(
            $element->isEnabled(),
            "Expected element [{$field}] to be enabled, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given field is disabled.
     *
     * @param  string  $field
     * @return $this
     */
    public function assertDisabled($field)
    {
        $element = $this->resolver->resolveForField($field);

        PHPUnit::assertFalse(
            $element->isEnabled(),
            "Expected element [{$field}] to be disabled, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given button is enabled.
     *
     * @param  string  $button
     * @return $this
     */
    public function assertButtonEnabled($button)
    {
        $element = $this->resolver->resolveForButtonPress($button);

        PHPUnit::assertTrue(
            $element->isEnabled(),
            "Expected button [{$button}] to be enabled, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given button is disabled.
     *
     * @param  string  $button
     * @return $this
     */
    public function assertButtonDisabled($button)
    {
        $element = $this->resolver->resolveForButtonPress($button);

        PHPUnit::assertFalse(
            $element->isEnabled(),
            "Expected button [{$button}] to be disabled, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given field is focused.
     *
     * @param  string  $field
     * @return $this
     */
    public function assertFocused($field)
    {
        $element = $this->resolver->resolveForField($field);

        PHPUnit::assertTrue(
            $this->driver->switchTo()->activeElement()->equals($element),
            "Expected element [{$field}] to be focused, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given field is not focused.
     *
     * @param  string  $field
     * @return $this
     */
    public function assertNotFocused($field)
    {
        $element = $this->resolver->resolveForField($field);

        PHPUnit::assertFalse(
            $this->driver->switchTo()->activeElement()->equals($element),
            "Expected element [{$field}] not to be focused, but it was."
        );

        return $this;
    }

    /**
     * Assert that the Vue component's attribute at the given key has the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string|null  $componentSelector
     * @return $this
     */
    public function assertVue($key, $value, $componentSelector = null)
    {
        $formattedValue = json_encode($value);

        PHPUnit::assertEquals(
            $value,
            $this->vueAttribute($componentSelector, $key),
            "Did not see expected value [{$formattedValue}] at the key [{$key}]."
        );

        return $this;
    }

    /**
     * Assert that a given Vue component data property does not match the given value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @param  string|null  $componentSelector
     * @return $this
     */
    public function assertVueIsNot($key, $value, $componentSelector = null)
    {
        $formattedValue = json_encode($value);

        PHPUnit::assertNotEquals(
            $value,
            $this->vueAttribute($componentSelector, $key),
            "Saw unexpected value [{$formattedValue}] at the key [{$key}]."
        );

        return $this;
    }

    /**
     * Assert that a given Vue component data propertys is an array and contains the given value.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  string|null  $componentSelector
     * @return $this
     */
    public function assertVueContains($key, $value, $componentSelector = null)
    {
        $attribute = $this->vueAttribute($componentSelector, $key);

        PHPUnit::assertIsArray(
            $attribute,
            "The attribute for key [{$key}] is not an array."
        );

        PHPUnit::assertContains($value, $attribute);

        return $this;
    }

    /**
     * Assert that a given Vue component data property is an array and does not contain the given value.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  string|null  $componentSelector
     * @return $this
     */
    public function assertVueDoesntContain($key, $value, $componentSelector = null)
    {
        return $this->assertVueDoesNotContain($key, $value, $componentSelector);
    }

    /**
     * Assert that a given Vue component data property is an array and does not contain the given value.
     *
     * @param  string  $key
     * @param  string  $value
     * @param  string|null  $componentSelector
     * @return $this
     */
    public function assertVueDoesNotContain($key, $value, $componentSelector = null)
    {
        $attribute = $this->vueAttribute($componentSelector, $key);

        PHPUnit::assertIsArray(
            $attribute,
            "The attribute for key [{$key}] is not an array."
        );

        PHPUnit::assertNotContains($value, $attribute);

        return $this;
    }

    /**
     * Retrieve the value of the Vue component's attribute at the given key.
     *
     * @param  string  $componentSelector
     * @param  string  $key
     * @return mixed
     */
    public function vueAttribute($componentSelector, $key)
    {
        $fullSelector = $this->resolver->format($componentSelector);

        return $this->driver->executeScript(
            "var el = document.querySelector('".$fullSelector."');".
            "if (typeof el.__vue__ !== 'undefined')".
            '    return el.__vue__.'.$key.';'.
            'try {'.
            '    var attr = el.__vueParentComponent.ctx.'.$key.';'.
            "    if (typeof attr !== 'undefined')".
            '        return attr;'.
            '} catch (e) {}'.
            'return el.__vueParentComponent.setupState.'.$key.';'
        );
    }
}
