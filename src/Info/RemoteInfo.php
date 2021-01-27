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
use function sprintf;

/**
 * Class RemoteMeta
 *
 * @package PhpGit\Info
 */
class RemoteInfo extends AbstractInfo
{
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
     * @var string
     */
    public $type = Git::URL_GIT;

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
     *  - http: "https://github.com/ulue/phpgit.git"
     *  - git: "git@github.com:ulue/phpgit.git"
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

    /**
     * @return string
     */
    public function getPath(): string
    {
        if ($this->path) {
            $this->path = $this->group . '/' . $this->repo;
        }

        return $this->path;
    }

    /**
     * @return string "git@github.com:ulue/swoft-component.git"
     */
    public function getGitUrl(): string
    {
        return sprintf('%s@%s:%s.git', Git::URL_GIT, $this->host, $this->getPath());
    }

    /**
     * @return string "https://github.com/ulue/phpgit.git"
     */
    public function getHttpUrl(): string
    {
        return sprintf('%s://%s/%s.git', Git::URL_HTTP, $this->host, $this->getPath());
    }

    public function __toString(): string
    {
        return sprintf('%s %s', $this->name, $this->url);
    }
}
