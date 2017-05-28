<?php

namespace Laravel\Dusk\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ScreenshotCaptured
{
    use Dispatchable;

    protected $filename;

    /**
     * Create a new event instance.
     *
     * @param $filename
     */
    public function __construct($filename)
    {
        $this->filename = $filename;
    }

}
