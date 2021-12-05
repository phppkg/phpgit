<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
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
 * @author  inhere
 */
class RemoteInfo extends AbstractInfo
{
    /**
     * The remote name
     *
     * @var string
     */
    public string $name = '';

    /**
     * The repo remote URL address
     *
     *  - http: "https://github.com/phppkg/swoft-component.git"
     *  - git: "git@github.com:phppkg/swoft-component.git"
     *
     * @var string
     */
    public string $url = '';

    // ----------- parts of the url

    /**
     * @var string
     */
    public string $type = Git::URL_GIT;

    /**
     * The url scheme. eg: git, http, https
     *
     * @var string
     */
    public string $scheme = '';

    /**
     * repo host. eg: github.com
     *
     * @var string
     */
    public string $host = '';

    /**
     * repo path. `path = group/repo`
     *
     * @var string
     */
    public string $path = '';

    /**
     * group name
     *
     * @var string
     */
    public string $group;

    /**
     * repo name
     *
     * @var string
     */
    public string $repo;

    /**
     * @param string $name
     * @param string $url
     *  - http: "https://github.com/phppkg/phpgit.git"
     *  - git: "git@github.com:phppkg/phpgit.git"
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
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->url !== '';
    }

    /**
     * @return bool
     */
    public function isInvalid(): bool
    {
        return $this->url === '';
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
     * @param bool $withSuffix
     *
     * @return string "git@github.com:phppkg/swoft-component.git"
     */
    public function getGitUrl(bool $withSuffix = false): string
    {
        $suffix = $withSuffix ? '.git' : '';

        return sprintf('%s@%s:%s%s', Git::URL_GIT, $this->host, $this->getPath(), $suffix);
    }

    /**
     * @param bool $withSuffix
     *
     * @return string "https://github.com/phppkg/phpgit.git"
     */
    public function getHttpUrl(bool $withSuffix = false): string
    {
        $scheme = $this->scheme;
        if (!str_contains($scheme, Git::URL_HTTP)) {
            $scheme = Git::URL_HTTP;
        }

        $suffix = $withSuffix ? '.git' : '';

        return sprintf('%s://%s/%s%s', $scheme, $this->host, $this->getPath(), $suffix);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s %s', $this->name, $this->url);
    }
}
