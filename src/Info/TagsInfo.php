<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit\Info;

use PhpGit\Concern\AbstractInfo;
use function count;

/**
 * Class TagMeta
 *
 * @package PhpGit\Info
 * @author inhere
 */
class TagsInfo extends AbstractInfo implements \Countable
{
    /**
     * @var array
     */
    public $tags = [];

    /**
     * @return string
     */
    public function first(): string
    {
        return $this->tags[0] ?? '';
    }

    /**
     * @return string
     */
    public function second(): string
    {
        return $this->tags[1] ?? '';
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->tags);
    }

    /**
     * @param bool  $filterEmpty
     * @param array $append
     *
     * @return array
     */
    public function toArray(bool $filterEmpty = false, array $append = []): array
    {
        return $this->tags;
    }
}
