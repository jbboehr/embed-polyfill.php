<?php

namespace jbboehr\EmbedPolyfill\Tests;

use EmbedExt\EmbedException;
use PHPUnit\Framework\TestCase;
use function EmbedExt\embed;
use function EmbedExt\embed_json;

class EmbedTest extends TestCase
{
    private const SAMPLE_VALUE = "foobar";
    private const SAMPLE_JSON_VALUE = ["foo" => "bar"];

    public function testBasic(): void
    {
        $this->assertSame(self::SAMPLE_VALUE, embed("sample.txt"));
    }

    public function testBasicJson(): void
    {
        $this->assertSame(self::SAMPLE_JSON_VALUE, embed_json("sample.json"));
    }

    public function testOnFileThatDoesNotExists(): void
    {
        $this->expectException(EmbedException::class);
        embed("does_not_exist.txt");
    }

    public function testOnFileThatDoesNotExistsJson(): void
    {
        $this->expectException(EmbedException::class);
        embed_json("does_not_exist.txt");
    }

    public function testWithADifferentWorkingDirectory(): void
    {
        $orig_cwd = getcwd();
        assert($orig_cwd !== false);
        try {
            chdir("src");
            $this->assertSame(self::SAMPLE_VALUE, embed("sample.txt"));
        } finally {
            chdir($orig_cwd);
        }
    }

    public function testWithADifferentWorkingDirectoryJson(): void
    {
        $orig_cwd = getcwd();
        assert($orig_cwd !== false);
        try {
            chdir("src");
            $this->assertSame(self::SAMPLE_JSON_VALUE, embed_json("sample.json"));
        } finally {
            chdir($orig_cwd);
        }
    }

    public function testWithEval(): void
    {
        $code = /** @lang PHP */<<<EOF
return \\EmbedExt\\embed("sample.txt");
EOF;
        $result = eval($code);
        $this->assertSame(self::SAMPLE_VALUE, $result);
    }

    public function testWithEvalJson(): void
    {
        $code = /** @lang PHP */<<<EOF
return \\EmbedExt\\embed_json("sample.json");
EOF;
        $result = eval($code);
        $this->assertSame(self::SAMPLE_JSON_VALUE, $result);
    }

    public function testIncludingFileTwiceIsOkay(): void
    {
        require __DIR__ . "/../src/functions.php";
        $this->assertTrue(class_exists(EmbedException::class));
    }

    public function testJsonDecodeError(): void
    {
        $this->expectException(EmbedException::class);
        embed_json("sample.txt");
    }
}
