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
use function array_map;
use function str_contains;
use function trim;

/**
 * Class BranchInfos
 *
 * @package PhpGit\Info
 */
class BranchInfos extends AbstractInfo
{
    /**
     * @var bool
     */
    protected bool $parsed = false;

    /**
     * raw branch lines by git branch
     *
     * @var string[]
     */
    protected array $branchLines = [];

    /**
     * @var ?BranchInfo
     */
    protected ?BranchInfo $currentBranch;

    /**
     * @var array{string: BranchInfo}
     */
    protected array $localBranches = [];

    /**
     * @var array{string: BranchInfo}
     */
    protected array $remoteBranches = [];

    /**
     * @param string $str from git branch command
     *
     * @return static
     */
    public static function fromString(string $str, bool $parse = true): self
    {
        return self::fromStrings(Str::split2Array($str, "\n"), $parse);
    }

    /**
     * @param string[] $lines from git branch command
     *
     * @return static
     */
    public static function fromStrings(array $lines, bool $parse = true): self
    {
        $self = new self();
        $self->setBranchLines($lines);

        return $parse ? $self->parse() : $self;
    }

    public function parse(): self
    {
        if ($this->parsed) {
            return $this;
        }

        $this->parsed = true;
        if (!$this->branchLines) {
            return $this;
        }

        $verbose   = false;
        $firstLine = $this->branchLines[0];
        if (str_contains(trim($firstLine, " *\t\n\r\0\x0B"), ' ')) {
            $verbose = true;
        }

        foreach ($this->branchLines as $line) {
            $branch = GitUtil::parseBranchLine($line, $verbose);
            if (!$branch['name']) {
                continue;
            }

            $brInfo = BranchInfo::new($branch);

            if ($brInfo->isRemoted()) {
                $this->remoteBranches[$brInfo->shortName] = $brInfo;
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
     * @return array
     */
    public function getBranchLines(): array
    {
        return $this->branchLines;
    }

    /**
     * @param array $branchLines
     *
     * @return BranchInfos
     */
    public function setBranchLines(array $branchLines): self
    {
        $this->branchLines = array_map('trim', $branchLines);
        return $this;
    }

    /**
     * @return BranchInfo|null
     */
    public function getCurrentBranch(): ?BranchInfo
    {
        return $this->currentBranch;
    }

    /**
     * @return array
     */
    public function getLocalBranches(): array
    {
        return $this->localBranches;
    }

    /**
     * @return array
     */
    public function getRemoteBranches(): array
    {
        return $this->remoteBranches;
    }
}
