<?php

namespace Laravel\Dusk\Concerns;

use Illuminate\Support\Str;
use PHPUnit_Framework_Assert as PHPUnit;
use Facebook\WebDriver\Exception\NoSuchElementException;

trait MakesAssertions
{
    /**
     * Assert that the page title is the given value.
     *
     * @param  string  $title
     * @return $this
     */
    public function assertTitle($title)
    {
        PHPUnit::assertEquals($title, $this->driver->getTitle());

        return $this;
    }

    /**
     * Assert that the page title contains the given value.
     *
     * @param  string  $title
     * @return $this
     */
    public function assertTitleContains($title)
    {
        PHPUnit::assertTrue(
            Str::contains($this->driver->getTitle(), $title)
        );

        return $this;
    }

    /**
     * Assert that the current URL path matches the given path.
     *
     * @param  string  $path
     * @return $this
     */
    public function assertPathIs($path)
    {
        PHPUnit::assertEquals($path, parse_url(
            $this->driver->getCurrentURL()
        )['path']);

        return $this;
    }

    /**
     * Assert that the given cookie is present.
     *
     * @param  string  $name
     * @return $this
     */
    public function assertHasCookie($name)
    {
        PHPUnit::assertTrue(
            ! is_null($this->cookie($name)),
            "Did not find expected cookie [{$name}]."
        );

        return $this;
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
            $value, $actual,
            "Cookie [{$name}] had value [{$actual}], but expected [{$value}]."
        );

        return $this;
    }

    /**
     * Assert that a cookie has a given value.
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
     * Assert that the given text appears on the page.
     *
     * @param  string  $text
     * @return $this
     */
    public function assertSee($text)
    {
        return $this->assertSeeIn('', $text);
    }

    /**
     * Assert that the given text does not appear on the page.
     *
     * @param  string  $text
     * @return $this
     */
    public function assertDontSee($text)
    {
        return $this->assertDontSeeIn('', $text);
    }

    /**
     * Assert that the given text appears within the given selector.
     *
     * @param  string  $selector
     * @param  string  $text
     * @return $this
     */
    public function assertSeeIn($selector, $text)
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertTrue(
            Str::contains($element->getText(), $text),
            "Did not see expected text [{$text}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that the given text does not appear within the given selector.
     *
     * @param  string  $selector
     * @param  string  $text
     * @return $this
     */
    public function assertDontSeeIn($selector, $text)
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertFalse(
            Str::contains($element->getText(), $text),
            "Saw unexpected text [{$text}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that the given link is visible.
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
     * Assert that the given link is not visible.
     *
     * @param  string  $link
     * @return $this
     */
    public function assertDontSeeLink($link)
    {
        if ($this->resolver->prefix) {
            $message = "Saw unexpected link [{$link}] within [{$this->resolver->prefix}].";
        } else {
            $message = "Saw unexpected expected link [{$link}].";
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

        $selector = trim($this->resolver->format("a:contains('{$link}')"));

        $script = <<<JS
            var link = jQuery.find("{$selector}");
            return link.length > 0 && jQuery(link).is(':visible');
JS;

        return $this->driver->executeScript($script);
    }

    /**
     * Assert that the given input or text area contains the given value.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertInputValue($field, $value)
    {
        PHPUnit::assertEquals($value, $this->inputValue($field));

        return $this;
    }

    /**
     * Assert that the given input or text area does not contain the given value.
     *
     * @param  string  $field
     * @param  string  $value
     * @return $this
     */
    public function assertInputValueIsNot($field, $value)
    {
        PHPUnit::assertNotEquals($value, $this->inputValue($field));

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

        return $element->getTagName() == 'input'
                        ? $element->getAttribute('value')
                        : $element->getText();
    }

    /**
     * Assert that the given checkbox field is checked.
     *
     * @param  string  $field
     * @param  string  $value
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
     * Assert that the given checkbox field is not checked.
     *
     * @param  string  $field
     * @param  string  $value
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
     * Assert that the given select field has the given value selected.
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
     * Assert that the given select field does not have the given value selected.
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
     * Determine if the given value is selected for the given select field.
     *
     * @param  string  $field
     * @param  string  $value
     * @return bool
     */
    public function selected($field, $value)
    {
        $element = $this->resolver->resolveForSelection($field);

        return $element->getAttribute('value') === $value;
    }

    /**
     * Assert that the element at the given selector has the given value.
     *
     * @param  string  $selector
     * @param  string  $value
     * @return $this
     */
    public function assertValue($selector, $value)
    {
        $actual = $this->resolver->findOrFail($selector)->getAttribute('value');

        PHPUnit::assertEquals($value, $actual);

        return $this;
    }

    /**
     * Assert that the element with the given selector is visible.
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
     * Assert that the element with the given selector is not on the page.
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

        PHPUnit::assertTrue($missing, "Saw unexpected element [{$fullSelector}].");

        return $this;
    }

    /**
     * Assert that a JavaScript dialog with given message has been opened.
     *
     * @param  string  $message
     * @return $this
     */
    public function assertDialogOpened($message)
    {
        PHPUnit::assertEquals(
            $message, $this->driver->switchTo()->alert()->getText()
        );

        return $this;
    }
}
