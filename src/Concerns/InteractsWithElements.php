<?php

namespace Laravel\Dusk\Concerns;

use Illuminate\Support\Str;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Facebook\WebDriver\Remote\LocalFileDetector;
use Facebook\WebDriver\Interactions\WebDriverActions;

trait InteractsWithElements
{
    /**
     * Get all of the elements matching the given selector.
     *
     * @param  string  $selector
     * @return array
     */
    public function elements($selector)
    {
        return $this->resolver->all($selector);
    }

    /**
     * Get the element matching the given selector.
     *
     * @param  string  $selector
     * @return \Facebook\WebDriver\Remote\RemoteWebElement|null
     */
    public function element($selector)
    {
        return $this->resolver->find($selector);
    }

    /**
     * Click the element at the given selector.
     *
     * @param  string  $selector
     * @return $this
     */
    public function click($selector)
    {
        $this->resolver->findOrFail($selector)->click();

        return $this;
    }

    /**
     * Click the link with the given text.
     *
     * @param  string  $link
     * @return $this
     */
    public function clickLink($link)
    {
        $selector = trim($this->resolver->format("a:contains('{$link}')"));

        $this->driver->executeScript("$(\"{$selector}\")[0].click();");

        return $this;
    }

    /**
     * Directly get or set the value attribute of an input field.
     *
     * @param  string  $selector
     * @param  string|null  $value
     * @return $this
     */
    public function value($selector, $value = null)
    {
        if (! $value) {
            return $this->resolver->findOrFail($selector)->getAttribute('value');
        }

        $selector = $this->resolver->format($selector);

        $this->driver->executeScript(
            "document.querySelector('{$selector}').value = '{$value}';"
        );

        return $this;
    }

    /**
     * Get the text of the element matching the given selector.
     *
     * @param  string  $selector
     * @return string
     */
    public function text($selector)
    {
        return $this->resolver->findOrFail($selector)->getText();
    }

    /**
     * Get the given attribute from the element matching the given selector.
     *
     * @param  string  $selector
     * @param  string  $attribute
     * @return string
     */
    public function attribute($selector, $attribute)
    {
        return $this->resolver->findOrFail($selector)->getAttribute($attribute);
    }

    /**
     * Send the given keys to the element matching the given selector.
     *
     * @param  string  $selector
     * @param  dynamic  $keys
     * @return $this
     */
    public function keys($selector, ...$keys)
    {
        $this->resolver->findOrFail($selector)->sendKeys($this->parseKeys($keys));

        return $this;
    }

    /**
     * Parse the keys before sending to the keyboard.
     *
     * @param  array  $keys
     * @return array
     */
    protected function parseKeys($keys)
    {
        return collect($keys)->map(function ($key) {
            if (is_string($key) && Str::startsWith($key, '{') && Str::endsWith($key, '}')) {
                $key = constant(WebDriverKeys::class.'::'.strtoupper(trim($key, '{}')));
            }

            if (is_array($key) && Str::startsWith($key[0], '{')) {
                $key[0] = constant(WebDriverKeys::class.'::'.strtoupper(trim($key[0], '{}')));
            }

            return $key;
        })->all();
    }

    /**
     * Type the given value in the given field.
     *
     * @param  string  $value
     * @param  string  $field
     * @return $this
     */
    public function type($value, $field)
    {
        $this->resolver->resolveForTyping($field)->clear()->sendKeys($value);

        return $this;
    }

    /**
     * Clear the given field.
     *
     * @param  string  $field
     * @return $this
     */
    public function clear($field)
    {
        $this->resolver->resolveForTyping($field)->clear();

        return $this;
    }

    /**
     * Select the given value of a drop-down field.
     *
     * @param  string  $value
     * @param  string  $field
     * @return $this
     */
    public function select($value, $field)
    {
        $element = $this->resolver->resolveForSelection($field);

        $options = $element->findElements(WebDriverBy::tagName('option'));

        foreach ($options as $option) {
            if ($option->getAttribute('value') === $value) {
                $option->click();

                break;
            }
        }

        return $this;
    }

    /**
     * Select the given value of a radio button field.
     *
     * @param  string  $value
     * @param  string  $field
     * @return $this
     */
    public function radio($value, $field)
    {
        $this->resolver->resolveForRadioSelection($field, $value)->click();

        return $this;
    }

    /**
     * Check the given checkbox.
     *
     * @param  string  $field
     * @return $this
     */
    public function check($field)
    {
        $element = $this->resolver->resolveForChecking($field);

        if (! $element->isSelected()) {
            $element->click();
        }

        return $this;
    }

    /**
     * Uncheck the given checkbox.
     *
     * @param  string  $field
     * @return $this
     */
    public function uncheck($field)
    {
        $element = $this->resolver->resolveForChecking($field);

        if ($element->isSelected()) {
            $element->click();
        }

        return $this;
    }

    /**
     * Attach the given file to the field.
     *
     * @param  string  $path
     * @param  string  $field
     * @return $this
     */
    public function attach($path, $field)
    {
        $element = $this->resolver->resolveForAttachment($field);

        $element->setFileDetector(new LocalFileDetector)->sendKeys($path);

        return $this;
    }

    /**
     * Press the button with the given text or name.
     *
     * @param  string  $button
     * @return $this
     */
    public function press($button)
    {
        $this->resolver->resolveForButtonPress($button)->click();

        return $this;
    }

    /**
     * Press the button with the given text or name.
     *
     * @param  string  $button
     * @param  int  $seconds
     * @return $this
     */
    public function pressAndWaitFor($button, $seconds = 5)
    {
        $element = $this->resolver->resolveForButtonPress($button);

        $element->click();

        return $this->waitUsing($seconds, 100, function () use ($element) {
            return $element->isEnabled();
        });
    }

    /**
     * Drag an element to another element using selectors.
     *
     * @param  string  $from
     * @param  string  $to
     * @return $this
     */
    public function drag($from, $to)
    {
        (new WebDriverActions($this->driver))->dragAndDrop(
            $this->resolver->findOrFail($from), $this->resolver->findOrFail($to)
        )->perform();

        return $this;
    }
}
