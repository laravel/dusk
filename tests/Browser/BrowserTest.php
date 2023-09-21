<?php

namespace Laravel\Dusk\Tests\Browser;

use Laravel\Dusk\Browser;

class BrowserTest extends DuskTestCase
{
    public function test_it_can_browse_default_laravel_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Documentation');
        });
    }
}
