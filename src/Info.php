<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit;

use PhpGit\Info\BranchInfo;
use PhpGit\Info\RemoteInfo;

/**
 * Class Info - Meta
 *
 * @package PhpGit
 */
class Info
{
    /**
     * @param string $name
     * @param string $url
     *
     * @return RemoteInfo
     */
    public static function getRemote(string $name, string $url): RemoteInfo
    {
        return RemoteInfo::newByUrl($name, $url);
    }
}
