<?php

namespace Innobird\Dusky;

abstract class Page
{
    /**
     * Get the URL for the page.
     *
     * @return string
     */
    abstract public function url();

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
     * Get the global element shortcuts for the site.
     *
     * @return array
     */
    public static function siteElements()
    {
        return [];
    }
}
