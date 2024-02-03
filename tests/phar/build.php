#!/usr/bin/env -S php -dphar.readonly=0
<?php

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
