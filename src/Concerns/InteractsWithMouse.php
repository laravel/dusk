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
     * @param integer $x
     * @param integer $y
     */
    public function mouseMoveByOffset($x_offset, $y_offset)
    {
        (new WebDriverActions($this->driver))->moveByOffset(
            $x_offset, $y_offset
        )->perform();

        return $this;
    }
}
