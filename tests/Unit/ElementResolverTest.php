<?php

namespace Laravel\Dusk\Tests\Unit;

use Facebook\WebDriver\Exception\NoSuchElementException;
use InvalidArgumentException;
use Laravel\Dusk\ElementResolver;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use stdClass;

class ElementResolverTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    /**
     * @throws \Exception
     */
    public function test_resolve_for_typing_resolves_by_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForTyping('#foo'));
    }

    public function test_resolve_for_typing_falls_back_to_selectors_without_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForTyping('foo'));
    }

    public function test_resolve_for_selection_resolves_by_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForSelection('#foo'));
    }

    public function test_resolve_for_selection_falls_back_to_selectors_without_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForSelection('foo'));
    }

    public function test_resolve_for_radio_selection_resolves_by_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForRadioSelection('#foo'));
    }

    public function test_resolve_for_radio_selection_falls_back_to_selectors_without_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForRadioSelection('foo', 'value'));
    }

    public function test_resolve_for_radio_selection_throws_exception_without_id_and_without_value()
    {
        $driver = m::mock(stdClass::class);
        $resolver = new ElementResolver($driver);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('No value was provided for radio button [foo].');

        $resolver->resolveForRadioSelection('foo');
    }

    public function test_resolve_for_checking_resolves_by_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForChecking('#foo'));
    }

    public function test_resolve_for_checking_falls_back_to_selectors_without_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForChecking('foo'));
    }

    public function test_resolve_for_attachment_resolves_by_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForAttachment('#foo'));
    }

    public function test_resolve_for_attachment_falls_back_to_selectors_without_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForAttachment('foo'));
    }

    public function test_resolve_for_field_resolves_by_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForField('#foo'));
    }

    public function test_resolve_for_field_falls_back_to_selectors_without_id()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertSame('foo', $resolver->resolveForField('foo'));
    }

    public function test_format_correctly_formats_selectors()
    {
        $resolver = new ElementResolver(new stdClass);
        $this->assertSame('body #modal', $resolver->format('#modal'));

        $resolver = new ElementResolver(new stdClass, 'prefix');
        $this->assertSame('prefix #modal', $resolver->format('#modal'));

        $resolver = new ElementResolver(new stdClass, 'prefix');
        $resolver->pageElements(['@modal' => '#modal']);
        $this->assertSame('prefix #modal', $resolver->format('@modal'));

        $resolver = new ElementResolver(new stdClass, 'prefix');
        $resolver->pageElements([
            '@modal' => '#first',
            '@modal-second' => '#second',
        ]);
        $this->assertSame('prefix #first', $resolver->format('@modal'));
        $this->assertSame('prefix #second', $resolver->format('@modal-second'));
        $this->assertSame('prefix #first-third', $resolver->format('@modal-third'));
        $this->assertSame('prefix [dusk="missing-element"]', $resolver->format('@missing-element'));
        $this->assertSame('prefix [dusk="missing-element"] > div', $resolver->format('@missing-element > div'));
    }

    public function test_format_does_not_capture_closing_parenthesis_in_dusk_selector()
    {
        $resolver = new ElementResolver(new stdClass, 'prefix');
        $this->assertSame('prefix [dusk="products"] div:nth-child(2 of [dusk="product"])', $resolver->format('@products div:nth-child(2 of @product)'));
    }

    public function test_find_by_id_with_colon()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);

        $class = new ReflectionClass($resolver);
        $method = $class->getMethod('findById');

        $result = $method->invoke($resolver, '#frmLogin:strCustomerLogin_userID');

        $this->assertSame('foo', $result);
    }

    public function test_find_button_by_text_prefers_exact_match()
    {
        $createAppButton = m::mock(stdClass::class);
        $createAppButton->shouldReceive('getText')->andReturn('Create Application');

        $createButton = m::mock(stdClass::class);
        $createButton->shouldReceive('getText')->andReturn('Create');

        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElements')->andReturn([$createAppButton, $createButton]);

        $resolver = new ElementResolver($driver);

        $class = new ReflectionClass($resolver);
        $method = $class->getMethod('findButtonByText');

        // When searching for "Create", the exact match should be returned
        // instead of "Create Application" which merely contains "Create".
        $result = $method->invoke($resolver, 'Create');

        $this->assertSame($createButton, $result);
    }

    public function test_find_button_by_text_falls_back_to_contains_match()
    {
        $createAppButton = m::mock(stdClass::class);
        $createAppButton->shouldReceive('getText')->andReturn('Create Application');

        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElements')->andReturn([$createAppButton]);

        $resolver = new ElementResolver($driver);

        $class = new ReflectionClass($resolver);
        $method = $class->getMethod('findButtonByText');

        // When no exact match exists, the contains-based match should still work.
        $result = $method->invoke($resolver, 'Create');

        $this->assertSame($createAppButton, $result);
    }

    public function test_find_button_by_text_trims_whitespace_for_exact_match()
    {
        $button = m::mock(stdClass::class);
        $button->shouldReceive('getText')->andReturn('  Create  ');

        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElements')->andReturn([$button]);

        $resolver = new ElementResolver($driver);

        $class = new ReflectionClass($resolver);
        $method = $class->getMethod('findButtonByText');

        // Button text with surrounding whitespace should still match exactly.
        $result = $method->invoke($resolver, 'Create');

        $this->assertSame($button, $result);
    }

    public function test_all_or_fail_returns_elements()
    {
        $element1 = m::mock(stdClass::class);
        $element2 = m::mock(stdClass::class);

        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElements')->andReturn([$element1, $element2]);

        $resolver = new ElementResolver($driver);

        $result = $resolver->allOrFail('div');

        $this->assertCount(2, $result);
        $this->assertSame($element1, $result[0]);
        $this->assertSame($element2, $result[1]);
    }

    public function test_all_or_fail_throws_when_no_elements()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElements')->andReturn([]);
        $driver->shouldReceive('findElement')->andThrow(new NoSuchElementException('No element found'));

        $resolver = new ElementResolver($driver);

        $this->expectException(NoSuchElementException::class);

        $resolver->allOrFail('div');
    }
}
