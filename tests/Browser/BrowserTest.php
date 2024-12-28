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

    public function test_it_handle_wait_for_text_in()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/tests/wait-for-text-in')
                ->assertSeeIn('@copy-button', 'Copy')
                ->press('@copy-button')
                ->assertSeeIn('@copy-button', 'Copied!')
                ->waitForTextIn('@copy-button', 'Copy', 3);
        });
    }

    public function test_it_handles_assert_see_in_with_multiple_selection()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/tests/assert-see-in')
                ->assertSee('Hello World')
                ->assertDontSee('Gesundheit')
                ->assertSeeIn('', 'Hello World')
                ->assertSeeIn('#app > h1', 'Hello World')
                ->assertSeeIn('#app > div', 'Second')
                ->assertSeeIn('#app > div:nth-child(3)', 'Third')
                ->assertDontSeeIn('#app > div:nth-child(3)', 'Second');
        });
    }
}
