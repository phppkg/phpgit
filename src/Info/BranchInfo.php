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
use function explode;
use function str_starts_with;
use function substr;

/**
 * Class BranchMeta
 *
 * @package PhpGit\Info
 */
class BranchInfo extends AbstractInfo
{
    public const REMOTE_PREFIX = 'remotes/';

    /**
     * Is current branch
     *
     * @var bool
     */
    public bool $current = false;

    /**
     * The branch name. eg: fea_xx, remotes/origin/fea_xx
     *
     * @var string
     */
    public string $name = '';

    /**
     * The commit hash
     *
     * @var string
     */
    public string $hash = '';

    /**
     * The commit message
     *
     * @var string
     */
    public string $hashMsg = '';

    /**
     * The alias name
     *
     * @var string
     */
    public string $alias = '';

    /**
     * @var string
     */
    public string $remote = '';

    /**
     * only branch name
     *
     * @var string
     */
    public string $shortName = '';

    /**
     * @return bool
     */
    public function isRemoted(): bool
    {
        return $this->remote !== '';
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $this->shortName = $name;

        // is remote branch
        if (str_starts_with($this->name, self::REMOTE_PREFIX)) {
            [
                $this->remote,
                $this->shortName
            ] = explode('/', substr($name, 8), 2);
        }
    }
}
