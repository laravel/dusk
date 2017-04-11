<?php

use Facebook\WebDriver\Remote\RemoteWebElement;
use Laravel\Dusk\Concerns\MakesAssertions;
use Laravel\Dusk\ElementResolver;

class MakesAssertionTest extends PHPUnit_Framework_TestCase
{
    public function test_assert_see()
    {
        $mock = Mockery::mock(DuskTest::class)->makePartial();

        $resolver = Mockery::mock(ElementResolver::class);

        $mock->resolver = $resolver;

        $element = Mockery::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->once()->andReturn('The Case Sensitive expected value.');
        $resolver->shouldReceive('format')->once()->andReturn(null);
        $resolver->shouldReceive('findOrFail')->once()->andReturn($element);

        $mock->assertSee('Case Sensitive');
    }

    public function test_assert_see_case_insensitive()
    {
        $mock = Mockery::mock(DuskTest::class)->makePartial();

        $resolver = Mockery::mock(ElementResolver::class);

        $mock->resolver = $resolver;

        $element = Mockery::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->once()->andReturn('The case insensitive expected value.');
        $resolver->shouldReceive('format')->once()->andReturn(null);
        $resolver->shouldReceive('findOrFail')->once()->andReturn($element);

        $mock->assertSee('Case Insensitive', false);
    }

    public function test_assert_see_failure()
    {
        $this->expectException(PHPUnit_Framework_AssertionFailedError::class);

        $mock = Mockery::mock(DuskTest::class)->makePartial();

        $resolver = Mockery::mock(ElementResolver::class);

        $mock->resolver = $resolver;

        $element = Mockery::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->once()->andReturn('The expected value.');
        $resolver->shouldReceive('format')->once()->andReturn(null);
        $resolver->shouldReceive('findOrFail')->once()->andReturn($element);

        $mock->assertSee('actual value');
    }

    public function test_assert_see_failure_because_of_case_sensitivity()
    {
        $this->expectException(PHPUnit_Framework_AssertionFailedError::class);

        $mock = Mockery::mock(DuskTest::class)->makePartial();

        $resolver = Mockery::mock(ElementResolver::class);

        $mock->resolver = $resolver;

        $element = Mockery::mock(RemoteWebElement::class);
        $element->shouldReceive('getText')->once()->andReturn('The case sensitive expected value.');
        $resolver->shouldReceive('format')->once()->andReturn(null);
        $resolver->shouldReceive('findOrFail')->once()->andReturn($element);

        $mock->assertSee('Case Sensitive');
    }
}

class DuskTest
{
    use MakesAssertions;
}
