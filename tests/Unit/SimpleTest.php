<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class SimpleTest extends TestCase
{
    public function test_basic_assertion(): void
    {
        $this->assertTrue(true);
        $this->assertFalse(false);
    }

    public function test_math_operations(): void
    {
        $this->assertEquals(4, 2 + 2);
        $this->assertNotEquals(5, 2 + 2);
    }

    public function test_string_operations(): void
    {
        $this->assertStringContainsString('hello', 'hello world');
        $this->assertStringNotContainsString('goodbye', 'hello world');
    }

    public function test_array_operations(): void
    {
        $array = [1, 2, 3];
        $this->assertContains(2, $array);
        $this->assertNotContains(4, $array);
        $this->assertCount(3, $array);
    }
}
