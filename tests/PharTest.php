<?php

namespace jbboehr\EmbedPolyfill\Tests;

use PHPUnit\Framework\TestCase;

class PharTest extends TestCase
{
    public function testPhar(): void
    {
        if (!extension_loaded('phar')) {
            $this->markTestSkipped();
        }

        require __DIR__ . '/phar/test.phar';

        $this->assertSame("barbat", phar_test());
    }
}
