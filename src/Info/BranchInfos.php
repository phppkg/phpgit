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
use PhpGit\GitUtil;
use Toolkit\Stdlib\Str;
use function count;
use function in_array;
use function is_string;
use function str_contains;
use function trim;

/**
 * Class BranchInfos
 *
 * @package PhpGit\Info
 */
class BranchInfos extends AbstractInfo
{
    public const FROM_ALL    = 'ALL';
    public const FROM_LOCAL  = 'LOCAL';
    public const FROM_REMOTE = 'REMOTE';

    /**
     * @var bool
     */
    protected bool $parsed = false;

    /**
     * @var ?BranchInfo
     */
    protected ?BranchInfo $currentBranch;

    /**
     * local branches. key is short name: master
     *
     * @var array{string: BranchInfo}
     */
    protected array $localBranches = [];

    /**
     * remote branches. key is full name: origin/master
     *
     * @var array{string: BranchInfo}
     */
    protected array $remoteBranches = [];

    /**
     * @param string $str from git branch command
     *
     * @return static
     */
    public static function fromString(string $str): self
    {
        return self::fromStrings(Str::split2Array($str, "\n"));
    }

    /**
     * @param string[] $lines from git branch command
     *
     * @return static
     */
    public static function fromStrings(array $lines): self
    {
        return (new self())->parse($lines);
    }

    /**
     * @param array $lines
     *
     * @return $this
     */
    public function parse(array $lines): self
    {
        if ($this->parsed) {
            return $this;
        }

        $this->parsed = true;
        if (!$lines) {
            return $this;
        }

        $verbose   = false;
        $firstLine = $lines[0];
        if (str_contains(trim($firstLine, " *\t\n\r\0\x0B"), ' ')) {
            $verbose = true;
        }

        foreach ($lines as $line) {
            $branch = GitUtil::parseBranchLine($line, $verbose);
            if (!$branch['name']) {
                continue;
            }

            $brInfo = BranchInfo::new($branch);

            if ($brInfo->isRemoted()) {
                // fix: cannot use shortName, will override on have multi remote
                $this->remoteBranches[$brInfo->name] = $brInfo;
            } else {
                $this->localBranches[$brInfo->shortName] = $brInfo;

                if ($brInfo->current) {
                    $this->currentBranch = $brInfo;
                }
            }
        }

        return $this;
    }

    /**
     * @param string $kw
     * @param int $limit
     *
     * @return BranchInfo[]
     */
    public function search(string $kw, int $limit = 3): array
    {
        $list = [];
        foreach ($this->localBranches as $name => $branch) {
            if (str_contains($name, $kw)) {
                $list[] = $branch;
            }

            if (count($list) >= $limit) {
                break;
            }
        }

        if (count($list) < $limit) {
            foreach ($this->remoteBranches as $name => $branch) {
                if (str_contains($name, $kw)) {
                    $list[] = $branch;
                }

                if (count($list) >= $limit) {
                    break;
                }
            }
        }

        return $list;
    }

    /**
     * @param string $name
     * @param string|array $from keywords {@see FROM_LOCAL} or remote names
     *
     * @return bool
     */
    public function hasBranch(string $name, string|array $from = self::FROM_LOCAL): bool
    {
        if (is_string($from)) {
            if ($from === self::FROM_LOCAL) {
                return isset($this->localBranches[$name]);
            }

            if ($from === self::FROM_ALL) {
                if (isset($this->localBranches[$name])) {
                    return true;
                }
                return $this->hasRemoteBranch($name);
            }

            if ($from === self::FROM_REMOTE) {
                return $this->hasRemoteBranch($name);
            }

            $remotes = [$from];
        } else {
            $remotes = $from;
        }

        return $this->hasRemoteBranch($name, $remotes);
    }

    /**
     * @param string $name name without remote.
     *
     * @return bool
     */
    public function hasLocalBranch(string $name): bool
    {
        return isset($this->localBranches[$name]);
    }

    /**
     * @param string $name name without remote.
     * @param string|array $remotes remote names
     *
     * @return bool
     */
    public function hasRemoteBranch(string $name, string|array $remotes = ''): bool
    {
        if ($remotes && is_string($remotes)) {
            $remotes = [$remotes];
        }

        foreach ($this->remoteBranches as $branch) {
            if (!$remotes || in_array($branch->remote, $remotes, true)) {
                if ($branch->shortName === $name) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param string $name name without remote.
     * @param string|array $from keywords {@see FROM_LOCAL} or remote names
     *
     * @return BranchInfo|null
     */
    public function getBranch(string $name, string|array $from = self::FROM_LOCAL): ?BranchInfo
    {
        if (is_string($from)) {
            if ($from === self::FROM_LOCAL) {
                return $this->localBranches[$name] ?? null;
            }

            if (!$from || $from === self::FROM_ALL) {
                return $this->localBranches[$name] ?? $this->getRemoteBranch($name);
            }

            if ($from === self::FROM_REMOTE) {
                return $this->getRemoteBranch($name);
            }

            $remotes = [$from];
        } else {
            $remotes = $from;
        }

        return $this->getRemoteBranch($name, $remotes);
    }

    /**
     * @param string $name name without remote.
     * @param string|array $remotes
     *
     * @return ?BranchInfo
     */
    public function getRemoteBranch(string $name, string|array $remotes = ''): ?BranchInfo
    {
        if ($remotes && is_string($remotes)) {
            $remotes = [$remotes];
        }

        foreach ($this->remoteBranches as $branch) {
            if (!$remotes || in_array($branch->remote, $remotes, true)) {
                if ($branch->shortName === $name) {
                    return $branch;
                }
            }
        }
        return null;
    }

    /**
     * @return BranchInfo|null
     */
    public function getCurrentBranch(): ?BranchInfo
    {
        return $this->currentBranch;
    }

    /**
     * @return  array{string: BranchInfo}
     */
    public function getLocalBranches(): array
    {
        return $this->localBranches;
    }

    /**
     * @return  array{string: BranchInfo}
     */
    public function getRemoteBranches(): array
    {
        return $this->remoteBranches;
    }
}
