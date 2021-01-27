<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Info;

use PhpGit\Concern\AbstractInfo;
use PhpGit\Git;
use PhpGit\GitUtil;

/**
 * Class RemoteMeta
 *
 * @package PhpGit\Info
 */
class RemoteInfo extends AbstractInfo
{
    /**
     * @var string
     */
    public $type = Git::TYPE_GIT;

    /**
     * The remote name
     *
     * @var string
     */
    public $name;

    /**
     * The repo remote URL address
     *
     *  - http: "https://github.com/ulue/swoft-component.git"
     *  - git: "git@github.com:ulue/swoft-component.git"
     *
     * @var string
     */
    public $url;

    // ----------- parts of the url

    /**
     * The url scheme. eg: git, http, https
     *
     * @var string
     */
    public $scheme;

    /**
     * repo host. eg: github.com
     *
     * @var string
     */
    public $host;

    /**
     * repo path. `path = group/repo`
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
     * @return RemoteInfo
     */
    public static function newByUrl(string $name, string $url): RemoteInfo
    {
        $info = GitUtil::parseRemoteUrl($url);
        // set name
        $info['name'] = $name;

        return new self($info);
    }
}
