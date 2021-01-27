<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit;

use PhpGit\Meta\RemoteMeta;

/**
 * Class Meta
 *
 * @package PhpGit
 */
class Meta
{
    public function __construct()
    {
    }

    public function getRemote(string $name): RemoteMeta
    {
    }
}
