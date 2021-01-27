<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Meta;

use PhpGit\AbstractMeta;

/**
 * Class RemoteMeta
 *
 * @package PhpGit\Meta
 */
class RemoteMeta extends AbstractMeta
{
    /**
     * @var string
     */
    public $name;

    /**
     * remote URL address
     *
     *  - http: "https://github.com/ulue/swoft-component.git"
     *  - git: "git@github.com:ulue/swoft-component.git"
     *
     * @var string
     */
    public $url;

    /**
     * @var string
     */
    public $host;

    /**
     * repo path.
     * path = group + repo
     *
     * @var string
     */
    public $path;

    /**
     * group name
     *
     * @var string
     */
    public $group;

    /**
     * repo name
     *
     * @var string
     */
    public $repo;

    /**
     * @param string $name
     * @param string $url
     *  - http: "https://github.com/ulue/swoft-component.git"
     *  - git: "git@github.com:ulue/swoft-component.git"
     *
     * @return RemoteMeta
     */
    public static function newByParse(string $name, string $url): RemoteMeta
    {
        $info = [
            'name' => $name,
            'url'  => $url,
        ];

        return new self($info);
    }
}
