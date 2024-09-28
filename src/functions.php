<?php
/**
 * Copyright (c) anno Domini nostri Jesu Christi MMXXIV John Boehr & contributors
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

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
