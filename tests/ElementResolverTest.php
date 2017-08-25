<?php

use PHPUnit\Framework\TestCase;
use Laravel\Dusk\ElementResolver;

class ElementResolverTest extends TestCase
{
    public function test_resolve_for_typing_resolves_by_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForTyping('#foo'));
    }

    public function test_resolve_for_typing_falls_back_to_selectors_without_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForTyping('foo'));
    }

    public function test_resolve_for_selection_resolves_by_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForSelection('#foo'));
    }

    public function test_resolve_for_selection_falls_back_to_selectors_without_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForSelection('foo'));
    }

    public function test_resolve_for_radio_selection_resolves_by_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForRadioSelection('#foo'));
    }

    public function test_resolve_for_radio_selection_falls_back_to_selectors_without_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForRadioSelection('foo', 'value'));
    }

    public function test_resolve_for_radio_selection_throws_exception_without_id_and_without_value()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('No value was provided for radio button [foo].');
        $resolver->resolveForRadioSelection('foo');
    }

    public function test_resolve_for_checking_resolves_by_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForChecking('#foo'));
    }

    public function test_resolve_for_checking_falls_back_to_selectors_without_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForChecking('foo'));
    }

    public function test_resolve_for_attachment_resolves_by_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForAttachment('#foo'));
    }

    public function test_resolve_for_attachment_falls_back_to_selectors_without_id()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);
        $this->assertEquals('foo', $resolver->resolveForAttachment('foo'));
    }

    public function test_format_correctly_formats_selectors()
    {
        $resolver = new ElementResolver(new StdClass);
        $this->assertEquals('body #modal', $resolver->format('#modal'));

        $resolver = new ElementResolver(new StdClass, 'prefix');
        $this->assertEquals('prefix #modal', $resolver->format('#modal'));

        $resolver = new ElementResolver(new StdClass, 'prefix');
        $resolver->pageElements(['@modal' => '#modal']);
        $this->assertEquals('prefix #modal', $resolver->format('@modal'));

        $resolver = new ElementResolver(new StdClass, 'prefix');
        $resolver->pageElements([
            '@modal' => '#first',
            '@modal-second' => '#second'
        ]);
        $this->assertEquals('prefix #first', $resolver->format('@modal'));
        $this->assertEquals('prefix #second', $resolver->format('@modal-second'));
        $this->assertEquals('prefix #first-third', $resolver->format('@modal-third'));
        $this->assertEquals('prefix [dusk="missing-element"]', $resolver->format('@missing-element'));
    }

    public function test_find_by_id_with_colon()
    {
        $driver = Mockery::mock(StdClass::class);
        $driver->shouldReceive('findElement')->once()->andReturn('foo');
        $resolver = new ElementResolver($driver);

        $class = new \ReflectionClass($resolver);
        $method = $class->getMethod('findById');
        $method->setAccessible(true);
        $result = $method->invoke($resolver, '#frmLogin:strCustomerLogin_userID');

        $this->assertEquals('foo', $result);
    }
}
