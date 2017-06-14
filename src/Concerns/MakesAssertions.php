<?php

namespace Laravel\Dusk\Concerns;

use Illuminate\Support\Str;
use PHPUnit\Framework\Assert as PHPUnit;
use Facebook\WebDriver\Exception\NoSuchElementException;

trait MakesAssertions
{
    /**
     * Assert that the page title is the given value.
     *
     * @param  string  $title
     * @param  string  $message
     * @return $this
     */
    public function assertTitle($title, $message = '')
    {
        PHPUnit::assertEquals($title, $this->driver->getTitle(), $message);

        return $this;
    }

    /**
     * Assert that the page title contains the given value.
     *
     * @param  string  $title
     * @param  string  $message
     * @return $this
     */
    public function assertTitleContains($title, $message = '')
    {
        PHPUnit::assertTrue(
            Str::contains($this->driver->getTitle(), $title), $message
        );

        return $this;
    }

    /**
     * Assert that the current URL path matches the given path.
     *
     * @param  string  $path
     * @param  string  $message
     * @return $this
     */
    public function assertPathIs($path, $message = '')
    {
        PHPUnit::assertEquals($path, parse_url(
            $this->driver->getCurrentURL()
        )['path'], $message);

        return $this;
    }

    /**
     * Assert that the current URL path begins with given path.
     *
     * @param  string  $path
     * @param  string  $message
     * @return $this
     */
    public function assertPathBeginsWith($path, $message = '')
    {
        PHPUnit::assertStringStartsWith($path, parse_url(
            $this->driver->getCurrentURL()
        )['path'], $message);

        return $this;
    }

    /**
     * Assert that the current URL path does not match the given path.
     *
     * @param  string  $path
     * @param  string  $message
     * @return $this
     */
    public function assertPathIsNot($path, $message = '')
    {
        PHPUnit::assertNotEquals($path, parse_url(
            $this->driver->getCurrentURL()
        )['path'], $message);

        return $this;
    }

    /**
     * Assert that the current URL path matches the given route.
     *
     * @param  string  $route
     * @param  array   $parameters
     * @param  string  $message
     * @return $this
     */
    public function assertRouteIs($route, $parameters = [], $message = '')
    {
        return $this->assertPathIs(route($route, $parameters, false), $message);
    }

    /**
     * Assert that a query string parameter is present and has a given value.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertQueryStringHas($name, $value = null, $message = '')
    {
        $output = $this->assertHasQueryStringParameter($name, $message);

        if (is_null($value)) {
            return $this;
        }

        PHPUnit::assertEquals(
            $value, $output[$name],
            $message ?: "Query string parameter [{$name}] had value [{$output[$name]}], but expected [{$value}]."
        );

        return $this;
    }

    /**
     * Assert that the given query string parameter is missing.
     *
     * @param  string  $name
     * @param  string  $message
     * @return $this
     */
    public function assertQueryStringMissing($name, $message = '')
    {
        $parsedUrl = parse_url($this->driver->getCurrentURL());

        if (! array_key_exists('query', $parsedUrl)) {
            PHPUnit::assertTrue(true);
            return $this;
        }

        parse_str($parsedUrl['query'], $output);

        PHPUnit::assertArrayNotHasKey(
            $name, $output,
            $message ?: "Found unexpected query string parameter [{$name}] in [".$this->driver->getCurrentURL()."]."
        );

        return $this;
    }

    /**
     * Assert that the given query string parameter is present.
     *
     * @param  string  $name
     * @param  string  $message
     * @return $this
     */
    protected function assertHasQueryStringParameter($name, $message = '')
    {
        $parsedUrl = parse_url($this->driver->getCurrentURL());

        PHPUnit::assertArrayHasKey(
            'query', $parsedUrl,
            $message ?: "Did not see expected query string in [".$this->driver->getCurrentURL()."]."
        );

        parse_str($parsedUrl['query'], $output);

        PHPUnit::assertArrayHasKey(
            $name, $output,
            $message ?: "Did not see expected query string parameter [{$name}] in [".$this->driver->getCurrentURL()."]."
        );

        return $output;
    }

    /**
     * Assert that the given cookie is present.
     *
     * @param  string  $name
     * @param  string  $message
     * @return $this
     */
    public function assertHasCookie($name, $message = '')
    {
        PHPUnit::assertTrue(
            ! is_null($this->cookie($name)),
            $message ?: "Did not find expected cookie [{$name}]."
        );

        return $this;
    }

    /**
     * Assert that an encrypted cookie has a given value.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  bool    $decrypt
     * @param  string  $message
     * @return $this
     */
    public function assertCookieValue($name, $value, $decrypt = true, $message = '')
    {
        $actual = $decrypt ? $this->cookie($name) : $this->plainCookie($name);

        PHPUnit::assertEquals(
            $value, $actual,
            $message ?: "Cookie [{$name}] had value [{$actual}], but expected [{$value}]."
        );

        return $this;
    }

    /**
     * Assert that a cookie has a given value.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertPlainCookieValue($name, $value, $message = '')
    {
        return $this->assertCookieValue($name, $value, false, $message);
    }

    /**
     * Assert that the given text appears on the page.
     *
     * @param  string  $text
     * @param  string  $message
     * @return $this
     */
    public function assertSee($text, $message = '')
    {
        return $this->assertSeeIn('', $text, $message);
    }

    /**
     * Assert that the given text does not appear on the page.
     *
     * @param  string  $text
     * @param  string  $message
     * @return $this
     */
    public function assertDontSee($text, $message = '')
    {
        return $this->assertDontSeeIn('', $text, $message);
    }

    /**
     * Assert that the given text appears within the given selector.
     *
     * @param  string  $selector
     * @param  string  $text
     * @param  string  $message
     * @return $this
     */
    public function assertSeeIn($selector, $text, $message = '')
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertTrue(
            Str::contains($element->getText(), $text),
            $message ?: "Did not see expected text [{$text}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that the given text does not appear within the given selector.
     *
     * @param  string  $selector
     * @param  string  $text
     * @param  string  $message
     * @return $this
     */
    public function assertDontSeeIn($selector, $text, $message = '')
    {
        $fullSelector = $this->resolver->format($selector);

        $element = $this->resolver->findOrFail($selector);

        PHPUnit::assertFalse(
            Str::contains($element->getText(), $text),
            $message ?: "Saw unexpected text [{$text}] within element [{$fullSelector}]."
        );

        return $this;
    }

    /**
     * Assert that the given source code is present on the page.
     *
     * @param  string  $code
     * @param  string  $message
     * @return $this
     */
    public function assertSourceHas($code, $message = '')
    {
        PHPUnit::assertContains(
            $code, $this->driver->getPageSource(),
            $message ?: "Did not find expected source code [{$code}]"
        );

        return $this;
    }

    /**
     * Assert that the given source code is not present on the page.
     *
     * @param  string  $code
     * @param  string  $message
     * @return $this
     */
    public function assertSourceMissing($code, $message = '')
    {
        PHPUnit::assertNotContains(
            $code, $this->driver->getPageSource(),
            $message ?: "Found unexpected source code [{$code}]"
        );

        return $this;
    }

    /**
     * Assert that the given link is visible.
     *
     * @param  string  $link
     * @param  string  $message
     * @return $this
     */
    public function assertSeeLink($link, $message = '')
    {
        if($message === '') {
            if ($this->resolver->prefix) {
                $message = "Did not see expected link [{$link}] within [{$this->resolver->prefix}].";
            } else {
                $message = "Did not see expected link [{$link}].";
            }
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
     * @param  string  $message
     * @return $this
     */
    public function assertDontSeeLink($link, $message = '')
    {
        if($message === '') {
            if ($this->resolver->prefix) {
                $message = "Saw unexpected link [{$link}] within [{$this->resolver->prefix}].";
            } else {
                $message = "Saw unexpected expected link [{$link}].";
            }
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
     * @param  string  $message
     * @return $this
     */
    public function assertInputValue($field, $value, $message = '')
    {
        PHPUnit::assertEquals($value, $this->inputValue($field), $message);

        return $this;
    }

    /**
     * Assert that the given input or text area does not contain the given value.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertInputValueIsNot($field, $value, $message = '')
    {
        PHPUnit::assertNotEquals($value, $this->inputValue($field), $message);

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
     * Assert that the given checkbox field is checked.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertChecked($field, $value = null, $message = '')
    {
        $element = $this->resolver->resolveForChecking($field, $value);

        PHPUnit::assertTrue(
            $element->isSelected(),
            $message ?: "Expected checkbox [{$field}] to be checked, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given checkbox field is not checked.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertNotChecked($field, $value = null, $message = '')
    {
        $element = $this->resolver->resolveForChecking($field, $value);

        PHPUnit::assertFalse(
            $element->isSelected(),
            $message ?: "Checkbox [{$field}] was unexpectedly checked."
        );

        return $this;
    }

    /**
     * Assert that the given radio field is selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    function assertRadioSelected($field, $value, $message = '')
    {
        $element = $this->resolver->resolveForRadioSelection($field, $value);

        PHPUnit::assertTrue(
            $element->isSelected(),
            $message ?: "Expected radio [{$field}] to be selected, but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given radio field is not selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertRadioNotSelected($field, $value = null, $message = '')
    {
        $element = $this->resolver->resolveForRadioSelection($field, $value);

        PHPUnit::assertFalse(
            $element->isSelected(),
            $message ?: "Radio [{$field}] was unexpectedly selected."
        );

        return $this;
    }

    /**
     * Assert that the given select field has the given value selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertSelected($field, $value, $message = '')
    {
        PHPUnit::assertTrue(
            $this->selected($field, $value),
            $message ?: "Expected value [{$value}] to be selected for [{$field}], but it wasn't."
        );

        return $this;
    }

    /**
     * Assert that the given select field does not have the given value selected.
     *
     * @param  string  $field
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertNotSelected($field, $value, $message = '')
    {
        PHPUnit::assertFalse(
            $this->selected($field, $value),
            $message ?: "Unexpected value [{$value}] selected for [{$field}]."
        );

        return $this;
    }

    /**
     * Assert that the given array of values are available to be selected.
     *
     * @param string  $field
     * @param array   $values
     * @param string  $message
     * @return $this
     */
    public function assertSelectHasOptions($field, array $values, $message = '')
    {
        PHPUnit::assertCount(
            count($values),
            $this->resolver->resolveSelectOptions($field, $values),
            $message ?: "Expected options [".implode(',', $values)."] for selection field [{$field}] to be available."
        );

        return $this;
    }

    /**
     * Assert that the given array of values are not available to be selected.
     *
     * @param string  $field
     * @param array   $values
     * @param string  $message
     * @return $this
     */
    public function assertSelectMissingOptions($field, array $values, $message = '')
    {
        PHPUnit::assertCount(
            0, $this->resolver->resolveSelectOptions($field, $values),
            $message ?: "Unexpected options [".implode(',', $values)."] for selection field [{$field}]."
        );

        return $this;
    }

    /**
     * Assert that the given value is available to be selected on the given field.
     *
     * @param string  $field
     * @param string  $value
     * @param string  $message
     * @return $this
     */
    public function assertSelectHasOption($field, $value, $message = '')
    {
        return $this->assertSelectHasOptions($field, [$value], $message);
    }

    /**
     * Assert that the given value is not available to be selected on the given field.
     *
     * @param string  $field
     * @param string  $value
     * @param string  $message
     * @return $this
     */
    public function assertSelectMissingOption($field, $value, $message = '')
    {
        return $this->assertSelectMissingOptions($field, [$value], $message);
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

        return (string) $element->getAttribute('value') === (string) $value;
    }

    /**
     * Assert that the element at the given selector has the given value.
     *
     * @param  string  $selector
     * @param  string  $value
     * @param  string  $message
     * @return $this
     */
    public function assertValue($selector, $value, $message = '')
    {
        $actual = $this->resolver->findOrFail($selector)->getAttribute('value');

        PHPUnit::assertEquals($value, $actual, $message);

        return $this;
    }

    /**
     * Assert that the element with the given selector is visible.
     *
     * @param  string  $selector
     * @param  string  $message
     * @return $this
     */
    public function assertVisible($selector, $message = '')
    {
        $fullSelector = $this->resolver->format($selector);

        PHPUnit::assertTrue(
            $this->resolver->findOrFail($selector)->isDisplayed(),
            $message ?: "Element [{$fullSelector}] is not visible."
        );

        return $this;
    }

    /**
     * Assert that the element with the given selector is not on the page.
     *
     * @param  string  $selector
     * @param  string  $message
     * @return $this
     */
    public function assertMissing($selector, $message = '')
    {
        $fullSelector = $this->resolver->format($selector);

        try {
            $missing = ! $this->resolver->findOrFail($selector)->isDisplayed();
        } catch (NoSuchElementException $e) {
            $missing = true;
        }

        PHPUnit::assertTrue($missing, $message ?: "Saw unexpected element [{$fullSelector}].");

        return $this;
    }

    /**
     * Assert that a JavaScript dialog with given message has been opened.
     *
     * @param  string  $dialogMessage
     * @param  string  $message
     * @return $this
     */
    public function assertDialogOpened($dialogMessage, $message = '')
    {
        PHPUnit::assertEquals(
            $dialogMessage, $this->driver->switchTo()->alert()->getText(), $message
        );

        return $this;
    }
}
