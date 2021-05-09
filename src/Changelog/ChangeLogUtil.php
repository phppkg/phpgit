<?php declare(strict_types=1);

namespace PhpGit\Changelog;

use function is_dir;
use function is_writable;
use function mkdir;
use function stripos;

/**
 * Class ChangeLogUtil
 * @package PhpGit\Changelog
 */
class ChangeLogUtil
{
    /**
     * @param string $msg
     *
     * @return bool
     */
    public static function isFixMsg(string $msg): bool
    {
        if (stripos($msg, 'bug') === 0 || stripos($msg, 'close') === 0 || stripos($msg, 'fix') === 0) {
            return true;
        }

        return stripos($msg, ' fix') > 0;
    }

    /**
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return bool
     */
    public static function mkdir(string $path, int $mode = 0775, bool $recursive = true): bool
    {
        return (is_dir($path) || !(!@mkdir($path, $mode, $recursive) && !is_dir($path))) && is_writable($path);
    }

}
