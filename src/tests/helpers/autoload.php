<?php
/**
 * Avoid require_once in all test cases and attempt to load classes here.
 *
 * @param string $class
 *
 * @return bool|mixed
 */
function testsuite_autoload($class) {
    static $base;
    if (null === $base) {
        $base = dirname(dirname(dirname(__FILE__))) . '/libraries';
    }
    /**
     * @todo PSR-0
     */
    if (substr($class, -9) === 'Interface') {
        $class = substr($class, 0, -9);
    }
    static $folders = array('adapters', 'models');
    foreach ($folders as $folder) {
        $path = sprintf("%s/%s/%s.php", $base, $folder, $class);
        if (true === file_exists($path)) {
            return include $path;
        }
    }
    return false;
}
spl_autoload_register('testsuite_autoload');
