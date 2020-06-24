<?php

namespace Laravel\Dusk\Concerns;

use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\WebDriverBy;

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
        } else {
            $this->resolver->findOrFail($selector)->click();
        }

        return $this;
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
     * @param  string  $selector
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
     * Perform a mouse click and hold the mouse button down.
     *
     * @return $this
     */
    public function clickAndHold()
    {
        (new WebDriverActions($this->driver))->clickAndHold()->perform();

        return $this;
    }

    /**
     * Perform a double click at the current mouse position.
     *
     * @return $this
     */
    public function doubleClick()
    {
        (new WebDriverActions($this->driver))->doubleClick()->perform();

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
