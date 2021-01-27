<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Asia/Shanghai');

require __DIR__ . '/BaseTestCase.php';

spl_autoload_register(static function ($class): void {
    $file = '';

    if (0 === strpos($class, 'PhpGit\Example\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('PhpGit\Example\\')));
        $file = dirname(__DIR__) . "/example/{$path}.php";
    } elseif (0 === strpos($class, 'PhpGitTest\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('PhpGitTest\\')));
        $file = __DIR__ . "/{$path}.php";
    } elseif (0 === strpos($class, 'PhpGit\\')) {
        $path = str_replace('\\', '/', substr($class, strlen('PhpGit\\')));
        $file = dirname(__DIR__) . "/src/{$path}.php";
    }

    if ($file && is_file($file)) {
        include $file;
    }
});

if (is_file(dirname(__DIR__, 3) . '/autoload.php')) {
    require dirname(__DIR__, 3) . '/autoload.php';
} elseif (is_file(dirname(__DIR__) . '/vendor/autoload.php')) {
    require dirname(__DIR__) . '/vendor/autoload.php';
}
