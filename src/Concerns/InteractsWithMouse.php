<?php

namespace Laravel\Dusk\Concerns;

use Facebook\WebDriver\Exception\ElementClickInterceptedException;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverKeys;
use Laravel\Dusk\Keyboard;
use Laravel\Dusk\OperatingSystem;

trait InteractsWithMouse
{
    /**
     * Move the mouse by offset X and Y.
     *
     * @param  int  $xOffset
     * @param  int  $yOffset
     * @return $this
     */
    public function moveMouse($xOffset, $yOffset)
    {
        (new WebDriverActions($this->driver))->moveByOffset(
            $xOffset, $yOffset
        )->perform();

        return $this;
    }

    /**
     * Move the mouse over the given selector.
     *
     * @param  string  $selector
     * @return $this
     */
    public function mouseover($selector)
    {
        $element = $this->resolver->findOrFail($selector);

        $this->driver->getMouse()->mouseMove($element->getCoordinates());

        return $this;
    }

    /**
     * Click the element at the given selector.
     *
     * @param  string|null  $selector
     * @return $this
     */
    public function click($selector = null)
    {
        if (is_null($selector)) {
            (new WebDriverActions($this->driver))->click()->perform();

            return $this;
        }

        foreach ($this->resolver->all($selector) as $element) {
            try {
                $element->click();

                return $this;
            } catch (ElementClickInterceptedException $e) {
                //
            }
        }

        throw $e ?? new NoSuchElementException("Unable to locate element with selector [{$selector}].");
    }

    /**
     * Click the topmost element at the given pair of coordinates.
     *
     * @param  int  $x
     * @param  int  $y
     * @return $this
     */
    public function clickAtPoint($x, $y)
    {
        $this->driver->executeScript("document.elementFromPoint({$x}, {$y}).click()");

        return $this;
    }

    /**
     * Click the element at the given XPath expression.
     *
     * @param  string  $expression
     * @return $this
     */
    public function clickAtXPath($expression)
    {
        $this->driver
            ->findElement(WebDriverBy::xpath($expression))
            ->click();

        return $this;
    }

    /**
     * Perform a mouse click and hold the mouse button down at the given selector.
     *
     * @param  string|null  $selector
     * @return $this
     */
    public function clickAndHold($selector = null)
    {
        if (is_null($selector)) {
            (new WebDriverActions($this->driver))->clickAndHold()->perform();
        } else {
            (new WebDriverActions($this->driver))->clickAndHold(
                $this->resolver->findOrFail($selector)
            )->perform();
        }

        return $this;
    }

    /**
     * Double click the element at the given selector.
     *
     * @param  string|null  $selector
     * @return $this
     */
    public function doubleClick($selector = null)
    {
        if (is_null($selector)) {
            (new WebDriverActions($this->driver))->doubleClick()->perform();
        } else {
            (new WebDriverActions($this->driver))->doubleClick(
                $this->resolver->findOrFail($selector)
            )->perform();
        }

        return $this;
    }

    /**
     * Right click the element at the given selector.
     *
     * @param  string|null  $selector
     * @return $this
     */
    public function rightClick($selector = null)
    {
        if (is_null($selector)) {
            (new WebDriverActions($this->driver))->contextClick()->perform();
        } else {
            (new WebDriverActions($this->driver))->contextClick(
                $this->resolver->findOrFail($selector)
            )->perform();
        }

        return $this;
    }

    /**
     * Control click the element at the given selector.
     *
     * @param  string|null  $selector
     * @return $this
     */
    public function controlClick($selector = null)
    {
        return $this->withKeyboard(function (Keyboard $keyboard) use ($selector) {
            $key = OperatingSystem::onMac() ? WebDriverKeys::META : WebDriverKeys::CONTROL;

            $keyboard->press($key);
            $this->click($selector);
            $keyboard->release($key);
        });
    }

    /**
     * Release the currently clicked mouse button.
     *
     * @return $this
     */
    public function releaseMouse()
    {
        (new WebDriverActions($this->driver))->release()->perform();

        return $this;
    }
}
