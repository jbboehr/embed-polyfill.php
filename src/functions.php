<?php

namespace EmbedExt;

if (!function_exists("\\EmbedExt\\embed")) {
    function embed(string $file): string
    {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $source_file = $bt[0]['file'] ?? null;
        assert($source_file !== null);
        $dir = dirname($source_file);
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path)) {
            throw new EmbedException(sprintf("File does not exist: %s", $path));
        }
        $contents = file_get_contents($path);
        // file_get_contents doesn't really seem to return false with these arguments...
        // tried a directory, and a non-existent file is covered by the file_exists above
        assert($contents !== false);
        return $contents;
    }
}

if (!function_exists("\\EmbedExt\\embed_json")) {
    function embed_json(string $file): mixed
    {
        $bt = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 1);
        $source_file = $bt[0]['file'] ?? null;
        assert($source_file !== null);
        $dir = dirname($source_file);
        $path = $dir . DIRECTORY_SEPARATOR . $file;
        if (!file_exists($path)) {
            throw new EmbedException(sprintf("File does not exist: %s", $path));
        }
        $contents = file_get_contents($path);
        // file_get_contents doesn't really seem to return false with these arguments...
        // tried a directory, and a non-existent file is covered by the file_exists above
        assert($contents !== false);
        try {
            return json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new EmbedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

if (!class_exists(EmbedException::class, false)) {
    class EmbedException extends \RuntimeException
    {
    }
}
