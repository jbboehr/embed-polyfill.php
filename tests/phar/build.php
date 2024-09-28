#!/usr/bin/env -S php -dphar.readonly=0
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

ini_set('phar.readonly', 0);

$phar = new Phar('test.phar', 0, 'test.phar');
$phar->buildFromIterator(
    new ArrayIterator([
        'script.php' => fopen(__DIR__ . '/script.php', 'rb'),
        'phar_sample.txt' => fopen(__DIR__ . '/phar_sample.txt', 'rb'),
    ])
);

$stub = file_get_contents(__DIR__ . '/index.php');
assert($stub !== false);
$phar->setStub($stub);
