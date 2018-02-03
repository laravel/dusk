<?php

namespace Laravel\Dusk\Concerns;

use Facebook\WebDriver\Interactions\WebDriverActions;

trait InteractsWithMouse
{
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
     * Move the mouse by some offset x and y.
     * 
     * @param integer $xOffset
     * @param integer $yOffset
     * @return $this
     */
    public function mouseMoveByOffset($xOffset, $yOffset)
    {
        (new WebDriverActions($this->driver))->moveByOffset(
            $xOffset, $yOffset
        )->perform();

        return $this;
    }

    /**
     * Perform click action at the current mouse position.
     * Use after mouseMoveByOffset or other method 
     * which combines some selector.
     * 
     * @return $this
     */
    public function mouseClick()
    {
        (new WebDriverActions($this->driver))->click()->perform();

        return $this;
    }

    /**
     * Perform click and hold mouse action at the current mouse position.
     * 
     * @return $this
     */
    public function mouseClickAndHold()
    {
        (new WebDriverActions($this->driver))->clickAndHold()->perform();

        return $this;
    }

    /**
     * Perform context click mouse action at the current mouse position.
     * 
     * @return $this
     */
    public function mouseContextClick()
    {
        (new WebDriverActions($this->driver))->contextClick()->perform();

        return $this;
    }

    /**
     * Perform double click action at the current mouse position.
     * 
     * @return $this
     */
    public function mouseDoubleClick()
    {
        (new WebDriverActions($this->driver))->doubleClick()->perform();

        return $this;
    }

    /**
     * Release currenctly clicked mouse button.
     * 
     * @return $this
     */
    public function mouseRelease()
    {
        (new WebDriverActions($this->driver))->release()->perform();

        return $this;
    }
    
}
