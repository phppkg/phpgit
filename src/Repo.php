<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit;

use Generator;
use InvalidArgumentException;
use PhpGit\Info\BranchInfo;
use PhpGit\Info\RemoteInfo;
use Toolkit\Cli\Cli;
use function strpos;

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
     * @var Git
     */
    private $git;

    /**
     * @var Info
     */
    private $info;

    /**
     * @var string
     */
    private $repoDir;

    /**
     * @var string
     */
    private $defaultRemote = Git::DEFAULT_REMOTE;

    /**
     * @var string[][]
     */
    private $remotes = [];

    /**
     * @var RemoteInfo[]
     */
    private $remoteInfos = [];

    /**
     * @var string[]
     */
    private $branchNames = [];

    /**
     * @var BranchInfo[]
     */
    private $branchInfos = [];

    /**
     * @var string
     */
    private $platform;

    /**
     * @var string
     */
    private $currentBranch;

    /**
     * @var string
     */
    private $lastCommitId;

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
     * @param mixed  ...$args
     *
     * @return CommandBuilder
     */
    public function newCmd(string $cmd, string ...$args): CommandBuilder
    {
        return $this->ensureGit()->newCmd($cmd, ...$args);
    }

    /**
     * @param string $cmd
     * @param mixed  ...$args
     */
    public function execAndOutput(string $cmd, ...$args): void
    {
        $git = $this->ensureGit();
        $out = $git->exec($cmd, ...$args);

        Cli::println($out);
    }

    /**
     * @param string $cmd
     * @param mixed  ...$args
     *
     * @return mixed
     */
    public function exec(string $cmd, ...$args)
    {
        $git = $this->ensureGit();

        return $git->exec($cmd, ...$args);
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
        $key = $name . '.' . $type;
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
        //     'fetch' => 'https://github.com/phpcom-lab/phpgit.git',
        //     'push' => 'git@github.com:phpcom-lab/phpgit.git',
        //  ),
        if ($type === 'fetch') {
            return $remotes[$name]['fetch'] ?? '';
        }

        return $remotes[$name]['push'] ?? '';
    }

    /**
     * @param bool $refresh
     *
     * @return array
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
        $this->getRemotes();

        return !isset($remotes[$name]);
    }

    protected function loadDefaultRemoteInfo(): void
    {
        $info = $this->getRemoteInfo();

        if (strpos($info->host, self::PLATFORM_GITHUB) !== false) {
            $this->platform = self::PLATFORM_GITHUB;
        } elseif (strpos($info->host, self::PLATFORM_GITLAB) !== false) {
            $this->platform = self::PLATFORM_GITLAB;
        } else {
            $this->platform = self::PLATFORM_CUSTOM;
        }
    }

    /**
     * @return string
     */
    public function getLastTagName(): string
    {
        $git = $this->ensureGit();

        $cmdLine = 'git fetch --tags';
        $git->runCmdLine($cmdLine);

        // $cmdLine = 'git describe --tags $(git rev-list --tags --max-count=1)';
        $cmdLine = 'git describe --abbrev=0 --tags';

        return $git->runCmdLine($cmdLine, true);
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
     * @return string
     */
    public function getLastCommitId(bool $refresh = false): string
    {
        if (false === $refresh && null !== $this->lastCommitId) {
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
            if (strpos($file, 'M ') === 0) {
                yield substr($file, 2);

                // deleted files
            } elseif (strpos($file, 'D ') === 0) {
                yield substr($file, 3);

              // new files
            } elseif (strpos($file, '?? ') === 0) {
                yield substr($file, 3);
            }
        }
    }

    /**
     * @return string[]
     */
    public function getInfo(): array
    {
        $remoteUrls = [];
        foreach ($this->getRemotes() as $name => $urls) {
            $remoteUrls[$name] = $urls['fetch'];
        }

        $repoInfo = [
            'platformName' => $this->getPlatform(),
            'currentBranch' => $this->getCurrentBranch(),
            'lastCommitId'  => $this->getLastCommitId(),
            'remoteList'    => $remoteUrls,
        ];

        if ($tagName = $this->getLastTagName()) {
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
