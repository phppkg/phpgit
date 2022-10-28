<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGit;

use Generator;
use PhpGit\Info\BranchInfo;
use PhpGit\Info\BranchInfos;
use PhpGit\Info\RemoteInfo;
use PhpGit\Info\StatusInfo;
use Toolkit\Cli\Cli;

/**
 * Class Repo
 *
 * @package PhpGit
 */
class Repo
{
    public const PLATFORM_GITHUB = 'github';
    public const PLATFORM_GITLAB = 'gitlab';
    public const PLATFORM_CUSTOM = 'custom';

    /**
     * @var Git|null
     */
    private ?Git $git = null;

    /*
     * @var Info
     */
    // private $info;

    /**
     * @var string
     */
    private string $repoDir;

    /**
     * @var string
     */
    private string $defaultRemote = Git::DEFAULT_REMOTE;

    /**
     * @var array{string: array}
     */
    private array $remotes = [];

    /**
     * @var RemoteInfo[]
     */
    private array $remoteInfos = [];

    /**
     * @var string[]
     */
    private array $branchNames = [];

    /**
     * @var BranchInfos|null
     */
    private BranchInfos|null $branchInfos = null;

    /**
     * @var string
     */
    private string $platform = '';

    /**
     * @var string|null
     */
    private ?string $currentBranch = null;

    /**
     * @var string 'commitId message'
     */
    private string $lastCommit = '';

    /**
     * @var string
     */
    private string $lastCommitId = '';

    /**
     * @var StatusInfo|null
     */
    private ?StatusInfo $statusInfo = null;

    /**
     * @param string $repoDir
     *
     * @return static
     */
    public static function new(string $repoDir = ''): self
    {
        return new self($repoDir);
    }

    /**
     * @param Git $git
     *
     * @return static
     */
    public static function newByGit(Git $git): self
    {
        $self = new self($git->getDirectory());
        $self->setGit($git);

        return $self;
    }

    /**
     * Class constructor.
     *
     * @param string $repoDir
     */
    public function __construct(string $repoDir = '')
    {
        $this->repoDir = $repoDir;
    }

    /**
     * @param string $cmd
     * @param mixed ...$args
     *
     * @return CmdBuilder
     */
    public function newCmd(string $cmd, string ...$args): CmdBuilder
    {
        return $this->ensureGit()->newCmd($cmd, ...$args);
    }

    /**
     * @param string $cmd
     * @param mixed ...$args
     */
    public function execAndOutput(string $cmd, ...$args): void
    {
        $git = $this->ensureGit();
        $out = $git->exec($cmd, ...$args);

        Cli::println($out);
    }

    /**
     * @param string $cmd
     * @param mixed ...$args
     *
     * @return mixed
     */
    public function exec(string $cmd, ...$args): mixed
    {
        return $this->ensureGit()->exec($cmd, ...$args);
    }

    /**
     * @param bool $refresh
     *
     * @return StatusInfo
     */
    public function getStatusInfo(bool $refresh = false): StatusInfo
    {
        if (!$refresh && null !== $this->statusInfo) {
            return $this->statusInfo;
        }

        $text = $this->ensureGit()
            ->newCmd('status', '-bs', '-u')
            ->run(true);

        $this->statusInfo = StatusInfo::fromString($text);
        return $this->statusInfo;
    }

    /**
     * @param string $name
     * @param string $type allow: fetch, push
     *
     * @return RemoteInfo
     */
    public function getRemoteInfo(string $name = '', string $type = 'fetch'): RemoteInfo
    {
        $name = $name ?: $this->defaultRemote;
        $key  = $name . '.' . $type;
        if (isset($this->remoteInfos[$key])) {
            return $this->remoteInfos[$key];
        }

        $url = $this->getRemoteUrl($name, $type);

        // create
        if ($url) {
            $this->remoteInfos[$key] = RemoteInfo::newByUrl($name, $url);
        } else {
            // throw new InvalidArgumentException("The remote '$name' is not exists");
            $this->remoteInfos[$key] = RemoteInfo::new(['name' => $name]);
        }

        return $this->remoteInfos[$key];
    }

    /**
     * @param string $name
     * @param string $type allow: fetch, push
     *
     * @return string
     */
    public function getRemoteUrl(string $name = '', string $type = 'fetch'): string
    {
        $remotes = $this->getRemotes();

        $name = $name ?: $this->defaultRemote;
        if (!isset($remotes[$name])) {
            return '';
        }

        // 'origin' => array (
        //     'fetch' => 'https://github.com/phppkg/phpgit.git',
        //     'push' => 'git@github.com:phppkg/phpgit.git',
        //  ),
        if ($type === 'fetch') {
            return $remotes[$name]['fetch'] ?? '';
        }

        return $remotes[$name]['push'] ?? '';
    }

    /**
     * @param bool $refresh
     *
     * @return array = [
     *     'origin' => [
     *          'fetch' => 'https://github.com/phppkg/phpgit.git',
     *          'push' => 'git@github.com:phppkg/phpgit.git',
     *      ]
     * ]
     */
    public function getRemotes(bool $refresh = false): array
    {
        if (false === $refresh && $this->remotes) {
            return $this->remotes;
        }

        $this->remotes = $this->ensureGit()->remote->getList();

        return $this->remotes;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRemote(string $name): bool
    {
        return isset($this->getRemotes()[$name]);
    }

    protected function loadDefaultRemoteInfo(): void
    {
        $info = $this->getRemoteInfo();

        if (str_contains($info->host, self::PLATFORM_GITHUB)) {
            $this->platform = self::PLATFORM_GITHUB;
        } elseif (str_contains($info->host, self::PLATFORM_GITLAB)) {
            $this->platform = self::PLATFORM_GITLAB;
        } else {
            $this->platform = self::PLATFORM_CUSTOM;
        }
    }

    /**
     * @param bool $refresh
     *
     * @return string
     */
    public function getLastTagName(bool $refresh = false): string
    {
        $git = $this->ensureGit();
        $val = $git->isQuietRun();

        $str = $git->setQuietRun(true)->getLastTagName($refresh);
        $git->setQuietRun($val);

        return $str;
    }

    /**
     * @param bool $refresh
     *
     * @return string
     */
    public function getCurrentBranch(bool $refresh = false): string
    {
        if (false === $refresh && null !== $this->currentBranch) {
            return $this->currentBranch;
        }

        $this->currentBranch = $this->ensureGit()->getCurrentBranch();

        return $this->currentBranch;
    }

    /**
     * @param bool $refresh
     *
     * @return BranchInfos
     */
    public function getBranchInfos(bool $refresh = false): BranchInfos
    {
        if ($refresh || !$this->branchInfos) {
            $str = $this->newCmd('branch', '-v', '--abbrev=7')
                ->setQuietRun(true)
                ->run(true);

            $this->branchInfos = BranchInfos::fromString($str);
        }

        return $this->branchInfos;
    }

    /**
     * @param string $name
     * @param string $remote
     *
     * @return BranchInfo
     */
    public function getBranchInfo(string $name, string $remote = ''): BranchInfo
    {
        $bis = $this->getBranchInfos();

        $from = BranchInfos::FROM_LOCAL;
        if ($remote) {
            $from = BranchInfos::FROM_REMOTE;
            $name = $remote . '/' . $name;
        }

        return $bis->getByName($name, $from);
    }

    /**
     * @param bool $refresh
     *
     * @return string
     */
    public function getLastCommit(bool $refresh = false): string
    {
        if (false === $refresh && '' !== $this->lastCommit) {
            return $this->lastCommit;
        }

        $this->lastCommit = $this->ensureGit()->getLastCommit();

        return $this->lastCommit;
    }

    /**
     * @param bool $refresh
     *
     * @return string
     */
    public function getLastCommitId(bool $refresh = false): string
    {
        if (false === $refresh && $this->lastCommitId) {
            return $this->lastCommitId;
        }

        $this->lastCommitId = $this->ensureGit()->getLastCommitId();

        return $this->lastCommitId;
    }

    /**
     * find changed or new created files by git status.
     */
    public function getChangedFiles(): ?Generator
    {
        // -u expand dir's files
        $cmdLine = 'git status -s -u';
        // [, $output,] = Sys::run($cmdLine, $this->workDir);
        $output = $this->ensureGit()->runCmdLine($cmdLine, true);

        // 'D some.file'    deleted
        // ' M some.file'   modified
        // '?? some.file'   new file
        foreach (explode("\n", $output) as $file) {
            $file = trim($file);

            // modified files
            if (str_starts_with($file, 'M ')) {
                yield substr($file, 2);

                // deleted files
            } elseif (str_starts_with($file, 'D ')) {
                yield substr($file, 3);

                // new files
            } elseif (str_starts_with($file, '?? ')) {
                yield substr($file, 3);
            }
        }
    }

    /**
     * @param bool $refresh
     *
     * @return string[]
     */
    public function getInfo(bool $refresh = false): array
    {
        $remoteUrls = [];
        foreach ($this->getRemotes() as $name => $urls) {
            $remoteUrls[$name] = $urls['fetch'];
        }

        $repoInfo = [
            'platformName'  => $this->getPlatform(),
            'currentBranch' => $this->getCurrentBranch($refresh),
            'lastCommit'    => $this->getLastCommit($refresh),
            'remoteList'    => $remoteUrls,
        ];

        if ($tagName = $this->getLastTagName($refresh)) {
            $repoInfo['lastTagName'] = $tagName;
        }

        return $repoInfo;
    }

    /**
     * @return Git
     */
    private function ensureGit(): Git
    {
        if (!$this->git) {
            $this->git = new Git($this->repoDir);
        }

        return $this->git;
    }

    /**
     * @return string
     */
    public function getRepoDir(): string
    {
        return $this->repoDir;
    }

    /**
     * @param Git $git
     *
     * @return Repo
     */
    public function setGit(Git $git): Repo
    {
        $this->git = $git;
        return $this;
    }

    /**
     * @return Git
     */
    public function getGit(): Git
    {
        return $this->ensureGit();
    }

    /**
     * @param bool $printCmd
     */
    public function setPrintCmd(bool $printCmd): void
    {
        $this->ensureGit()->setPrintCmd($printCmd);
    }

    /**
     * @return string
     */
    public function getDefaultRemote(): string
    {
        return $this->defaultRemote;
    }

    /**
     * @param string $defaultRemote
     */
    public function setDefaultRemote(string $defaultRemote): void
    {
        $this->defaultRemote = $defaultRemote;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        if (!$this->platform) {
            $this->loadDefaultRemoteInfo();
        }

        return $this->platform;
    }

}
