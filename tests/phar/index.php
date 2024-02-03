<?php

Phar::mapPhar('test.phar');
require_once "phar://test.phar/script.php";

__HALT_COMPILER();
