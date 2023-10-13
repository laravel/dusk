<?php

namespace Laravel\Dusk\Tests\Unit;

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

    public function test_find_by_id_with_colon()
    {
        $driver = m::mock(stdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);

        $class = new ReflectionClass($resolver);
        $method = $class->getMethod('findById');
        $method->setAccessible(true);
        $result = $method->invoke($resolver, '#frmLogin:strCustomerLogin_userID');

        $this->assertSame('foo', $result);
    }
}
