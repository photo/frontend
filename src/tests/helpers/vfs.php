<?php
/**
 * @desc Display a helpful note instead.
 */
if (false === (@include_once 'vfsStream/vfsStream.php')) {
    echo "Please install vfs: " . PHP_EOL;
    echo "(sudo) pear channel-discover pear.bovigo.org" . PHP_EOL;
    echo "(sudo) pear install pat/vfsStream" . PHP_EOL;
    echo PHP_EOL;
    echo "In case it still fails, please fix your include_path in your php.ini:" . PHP_EOL;
    echo get_include_path() . PHP_EOL;
}
