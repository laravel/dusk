<?php

namespace Innobird\Dusky;

abstract class Component
{
    /**
     * Get the root selector associated with this component.
     *
     * @return string
     */
    abstract public function selector();

    /**
     * Get the element shortcuts for the page.
     *
     * @return array
     */
    public function elements()
    {
        return [];
    }

    /**
     * Allow this class to be used in place of a selector string.
     *
     * @return string
     */
    public function __toString()
    {
        return '';
    }
}
