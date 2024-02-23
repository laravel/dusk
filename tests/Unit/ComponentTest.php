<?php

namespace Laravel\Dusk\Tests\Unit;

use Laravel\Dusk\Browser;
use Laravel\Dusk\Component;
use Laravel\Dusk\Page;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use stdClass;

class ComponentTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function test_within_method_triggers_assertion()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent, function ($browser) {
            $this->assertTrue($browser->component->asserted);

            $browser->within($nested = new TestNestedComponent, function () use ($nested) {
                $this->assertTrue($nested->asserted);
            });
        });
    }

    public function test_within_method_resolver_prefix()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent, function ($browser) {
            $this->assertSame('body #component-root', $browser->resolver->prefix);

            $browser->within($nested = new TestNestedComponent, function ($browser) {
                $this->assertSame('body #component-root #nested-root', $browser->resolver->prefix);

                $browser->with('prefix', function ($browser) {
                    $this->assertSame('body #component-root #nested-root prefix', $browser->resolver->prefix);
                });
            });
        });
    }

    public function test_within_method_component_macros()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent, function ($browser) {
            $browser->doSomething();
            $this->assertTrue($browser->component->macroed);

            $browser->within($nested = new TestNestedComponent, function ($browser) use ($nested) {
                $browser->doSomething();
                $this->assertTrue($nested->macroed);
            });
        });
    }

    public function test_within_method_component_elements()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within($component = new TestComponent, function ($browser) {
            $this->assertEquals([
                '@component-alias' => '#component-alias',
                '@overridden-alias' => '#not-overridden',
            ], $browser->resolver->elements);

            $browser->within($nested = new TestNestedComponent, function ($browser) {
                $this->assertEquals([
                    '@nested-alias' => '#nested-alias',
                    '@overridden-alias' => '#overridden',
                    '@component-alias' => '#component-alias',
                ], $browser->resolver->elements);
            });
        });
    }

    public function test_within_method_root_selector_can_be_dusk_hook()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $component = new TestComponent;
        $component->selector = '@dusk-hook-root';

        $browser->within($component, function ($browser) {
            $this->assertSame('body [dusk="dusk-hook-root"]', $browser->resolver->prefix);
        });
    }

    public function test_within_method_root_selector_can_be_element_alias()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $component = new TestComponent;
        $component->selector = '@component-alias';

        $browser->within($component, function ($browser) {
            $this->assertSame('body #component-alias', $browser->resolver->prefix);
        });
    }

    public function test_within_method_component_overrides_page_macros()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->on($page = new TestPage);

        $browser->within($component = new TestComponent, function ($browser) {
            $browser->doSomething();

            $this->assertFalse($browser->page->macroed);
            $this->assertTrue($browser->component->macroed);

            $browser->doPageSpecificThing();

            $this->assertTrue($browser->page->macroed);
        });
    }

    public function test_within_method_chains()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->within(new TestComponent, function ($browser) {
            $this->assertInstanceOf(TestComponent::class, $browser->component);
            $this->assertTrue($browser->component->asserted);
            $this->assertSame('body #component-root', $browser->resolver->prefix);
            $this->assertFalse($browser->component->macroed);

            $browser->doSomething();
            $this->assertTrue($browser->component->macroed);
        })->within(new TestNestedComponent, function ($browser) {
            $this->assertInstanceOf(TestNestedComponent::class, $browser->component);
            $this->assertTrue($browser->component->asserted);
            $this->assertSame('body #nested-root', $browser->resolver->prefix);
            $this->assertFalse($browser->component->macroed);

            $browser->doSomething();
            $this->assertTrue($browser->component->macroed);
        });
    }

    public function test_component_method_triggers_assertion()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $scoped = $browser->component(new TestComponent);
        $this->assertTrue($scoped->component->asserted);

        $nested = $scoped->component(new TestNestedComponent);
        $this->assertTrue($nested->component->asserted);
    }

    public function test_component_method_resolver_prefix()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $scoped = $browser->component(new TestComponent);
        $this->assertSame('body #component-root', $scoped->resolver->prefix);

        $nested = $scoped->component(new TestNestedComponent);
        $this->assertSame('body #component-root #nested-root', $nested->resolver->prefix);

        $nested->with('prefix', function (Browser $browser) {
            $this->assertSame('body #component-root #nested-root prefix', $browser->resolver->prefix);
        });
    }

    public function test_component_method_component_macros()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $scoped = $browser->component(new TestComponent);
        $scoped->doSomething();
        $this->assertTrue($scoped->component->macroed);

        $nested = $scoped->component(new TestNestedComponent);
        $nested->doSomething();
        $this->assertTrue($nested->component->macroed);
    }

    public function test_component_method_component_elements()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $scoped = $browser->component(new TestComponent);
        $this->assertEquals([
            '@component-alias' => '#component-alias',
            '@overridden-alias' => '#not-overridden',
        ], $scoped->resolver->elements);

        $nested = $scoped->component(new TestNestedComponent);
        $this->assertEquals([
            '@nested-alias' => '#nested-alias',
            '@overridden-alias' => '#overridden',
            '@component-alias' => '#component-alias',
        ], $nested->resolver->elements);
    }

    public function test_component_method_root_selector_can_be_dusk_hook()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $component = new TestComponent;
        $component->selector = '@dusk-hook-root';

        $scoped = $browser->component($component);
        $this->assertSame('body [dusk="dusk-hook-root"]', $scoped->resolver->prefix);
    }

    public function test_component_method_root_selector_can_be_element_alias()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $component = new TestComponent;
        $component->selector = '@component-alias';

        $scoped = $browser->component($component);
        $this->assertSame('body #component-alias', $scoped->resolver->prefix);
    }

    public function test_component_method_overrides_page_macros()
    {
        $driver = m::mock(stdClass::class);
        $browser = new Browser($driver);

        $browser->on($page = new TestPage);

        $scoped = $browser->component(new TestComponent);
        $scoped->doSomething();
        $this->assertFalse($scoped->page->macroed);
        $this->assertTrue($scoped->component->macroed);

        $scoped->doPageSpecificThing();
        $this->assertTrue($scoped->page->macroed);
    }
}

class TestPage extends Page
{
    public $macroed = false;

    public function url()
    {
        return '/login';
    }

    public function doSomething()
    {
        $this->macroed = true;
    }

    public function doPageSpecificThing()
    {
        $this->macroed = true;
    }
}

class TestComponent extends Component
{
    public $selector = '#component-root';
    public $asserted = false;
    public $macroed = false;

    public function assert(Browser $browser)
    {
        $this->asserted = true;
    }

    public function selector()
    {
        return $this->selector;
    }

    public function doSomething()
    {
        $this->macroed = true;
    }

    public function elements()
    {
        return [
            '@component-alias' => '#component-alias',
            '@overridden-alias' => '#not-overridden',
        ];
    }
}

class TestNestedComponent extends Component
{
    public $asserted = false;
    public $macroed = false;

    public function selector()
    {
        return '#nested-root';
    }

    public function assert(Browser $browser)
    {
        $this->asserted = true;
    }

    public function doSomething()
    {
        $this->macroed = true;
    }

    public function elements()
    {
        return [
            '@nested-alias' => '#nested-alias',
            '@overridden-alias' => '#overridden',
        ];
    }
}
