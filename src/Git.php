<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit;

use PhpGit\Concern\ExecGitCommandTrait;
use PhpGit\Exception\GitException;

/**
 * # PhpGit
 *
 * [![GitHub tag (latest SemVer)](https://img.shields.io/github/tag/phpcom-lab/phpgit)](https://github.com/phpcom-lab/phpgit)
 * [![Github Actions Status](https://github.com/phpcom-lab/phpgit/workflows/Unit-tests/badge.svg)](https://github.com/phpcom-lab/phpgit/actions)
 *
 * PhpGit - A Git wrapper for PHP7.1+
 *
 * ## Requirements
 *
 * * PHP5.3
 * * Git
 *
 * ## Installation
 *
 * Update your composer.json and run `composer update`
 *
 * ``` json
 * {
 *     "require": {
 *         "phpcom-lab/phpgit": "dev-master"
 *     }
 * }
 * ```
 *
 * ## Basic Usage
 *
 * ``` php
 * <?php
 *
 * require __DIR__ . '/vendor/autoload.php';
 *
 * $git = new PhpGit\Git();
 * $git->clone('https://github.com/phpcom-lab/phpgit.git', '/path/to/repo');
 * $git->setRepository('/path/to/repo');
 * $git->remote->add('production', 'git://example.com/your/repo.git');
 * $git->add('README.md');
 * $git->commit('Adds README.md');
 * $git->checkout('release');
 * $git->merge('master');
 * $git->push();
 * $git->push('production', 'release');
 * $git->tag->create('v1.0.1', 'release');
 *
 * foreach ($git->tree('release') as $object) {
 *     if ($object['type'] == 'blob') {
 *         echo $git->show($object['file']);
 *     }
 * }
 * ```
 *
 * @author  Kazuyuki Hayashi <hayashi@valnur.net>
 * @license MIT
 *
 * @property-read Command\Add      $add
 * @property-read Command\Archive  $archive
 * @property-read Command\Blame    $blame
 * @property-read Command\Branch   $branch
 * @property-read Command\Cat      $cat
 * @property-read Command\Checkout $checkout
 * @property-read Command\GitClone $clone
 * @property-read Command\Commit   $commit
 * @property-read Command\Config   $config
 * @property-read Command\Describe $describe
 * @property-read Command\Fetch    $fetch
 * @property-read Command\Init     $init
 * @property-read Command\Log      $log
 * @property-read Command\Merge    $merge
 * @property-read Command\Mv       $mv
 * @property-read Command\Pull     $pull
 * @property-read Command\Push     $push
 * @property-read Command\Rebase   $rebase
 * @property-read Command\Remote   $remote
 * @property-read Command\Reset    $reset
 * @property-read Command\Rm       $rm
 * @property-read Command\Shortlog $shortlog
 * @property-read Command\Show     $show
 * @property-read Command\Stash    $stash
 * @property-read Command\Status   $status
 * @property-read Command\Tag      $tag
 * @property-read Command\Tree     $tree
 *
 * @method add(string|array $file, $options = [])                           Add file contents to the index
 * @method archive(string $file, $tree = null, $path = null, $options = []) Create an archive of files from a named tree
 * @method blame(string $file = null, $hash = null)                         Returns the file lines with blame
 * @method branch($options = [])                                            List both remote-tracking branches and local branches
 * @method checkout(string $branch, $options = [])                          Checkout a branch or paths to the working tree
 * @method clone (string $repository, $path = null, $options = [])          Clone a repository into a new directory
 * @method commit(string $message = '', $options = [])                      Record changes to the repository
 * @method config($options = [])                                            List all variables set in config file
 * @method describe($committish = null, $options = [])                      Returns the most recent tag that is reachable from a commit
 * @method fetch(string $repository, $refspec = null, $options = [])        Fetches named heads or tags from one or more other repositories
 * @method init(string $path, $options = [])                                Create an empty git repository or reinitialize an existing one
 * @method log($path = null, $options = [])                                 Returns the commit logs
 * @method merge($commit, $message = null, $options = [])                   Incorporates changes from the named commits into the current branch
 * @method mv($source, $destination, $options = [])                         Move or rename a file, a directory, or a symlink
 * @method pull(string $repository = null, $refspec = null, $options = []) Fetch from and merge with another repository or a local branch
 * @method push(string $repository = null, $refspec = null, $options = []) Update remote refs along with associated objects
 * @method rebase($upstream = null, $branch = null, $options = [])          Forward-port local commits to the updated upstream head
 * @method array remote()                                                         Returns an array of existing remotes
 * @method reset($commit = null, $paths = [])                               Resets the index entries for all <paths> to their state at <commit>
 * @method rm($file, $options = [])                                         Remove files from the working tree and from the index
 * @method shortlog($commits = [])                                          Summarize 'git log' output
 * @method show($object, $options = [])                                     Shows one or more objects (blobs, trees, tags and commits)
 * @method stash()                                                          Save your local modifications to a new stash, and run git reset --hard to revert them
 * @method status($options = [])                                            Show the working tree status
 * @method tag()                                                            Returns an array of tags
 * @method tree(string $branch = 'master', string $path = '')               List the contents of a tree object
 */
class Git
{
    use ExecGitCommandTrait;

    public const URL_GIT  = 'git';
    public const URL_HTTP = 'http';

    public const GITHUB_HOST = 'github.com';

    public const DEFAULT_REMOTE = 'origin';

    // all supported git commands
    public const COMMANDS = [
        'add'      => Command\Add::class,
        'archive'  => Command\Archive::class,
        'blame'    => Command\Blame::class,
        'branch'   => Command\Branch::class,
        'cat'      => Command\Cat::class,
        'checkout' => Command\Checkout::class,
        'clone'    => Command\GitClone::class,
        'commit'   => Command\Commit::class,
        'config'   => Command\Config::class,
        'describe' => Command\Describe::class,
        // 'diff' => Command\Diff::class,     // Not implemented yet
        'fetch'    => Command\Fetch::class,
        'init'     => Command\Init::class,
        'log'      => Command\Log::class,
        'merge'    => Command\Merge::class,
        'mv'       => Command\Mv::class,
        'pull'     => Command\Pull::class,
        'push'     => Command\Push::class,
        'rebase'   => Command\Rebase::class,
        'remote'   => Command\Remote::class,
        'reset'    => Command\Reset::class,
        'rm'       => Command\Rm::class,
        'shortlog' => Command\Shortlog::class,
        'show'     => Command\Show::class,
        'stash'    => Command\Stash::class,
        'status'   => Command\Status::class,
        'tag'      => Command\Tag::class,
        'tree'     => Command\Tree::class,
    ];

    /** @var string */
    private $bin = 'git';

    /** @var integer */
    private $timeout = 60;

    /** @var string The git repo dir path. */
    private $directory;

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
     * @param string $repoDir
     *
     * @return Repo
     */
    public static function newRepo(string $repoDir): Repo
    {
        return Repo::new($repoDir);
    }

    /**
     * Initializes sub-commands
     *
     * @param string $repoDir
     */
    public function __construct(string $repoDir = '')
    {
        $this->directory = $repoDir;
    }

    /**
     * @param string $cmd
     * @param mixed  ...$args
     *
     * @return CommandBuilder
     */
    public function newCmd(string $cmd, string ...$args): CommandBuilder
    {
        return $this->getCommandBuilder($cmd, ...$args);
    }

    /**
     * Returns an instance of ProcessBuilder
     *
     * @param string   $command
     * @param string[] ...$args
     *
     * @return CommandBuilder
     */
    public function getCommandBuilder(string $command = '', ...$args): CommandBuilder
    {
        return CommandBuilder::create($command, ...$args)
            ->setBin($this->bin)
            ->setWorkDir($this->directory)
            ->setTimeout( $this->timeout);
    }

    /**
     * @param string $cmdLine
     * @param bool   $trimOutput
     *
     * @return string
     */
    public function runCmdLine(string $cmdLine, bool $trimOutput = false): string
    {
        $cmd = $this->getCommandBuilder();

        return $cmd->setCommandLine($cmdLine)->run($trimOutput);
    }

    /**
     * @return string
     */
    public function getLastCommitId(): string
    {
        // latest commit id by: git log --pretty=%H -n1 HEAD
        $cmdLine = 'git log --pretty=%H -n1 HEAD';

        return $this->runCmdLine($cmdLine, true);
    }

    /**
     * @return string
     */
    public function getCurrentBranch(): string
    {
        // 1. git symbolic-ref --short -q HEAD
        // 2. git rev-parse --abbrev-ref HEA
        // 3. git branch --show-current // Old version does not support
        $str = 'git symbolic-ref --short -q HEAD';

        return $this->runCmdLine($str, true);
    }

    /**
     * @return $this
     */
    public function getGit(): Git
    {
        return $this;
    }

    /**
     * Sets the Git binary path
     *
     * @param string $bin
     *
     * @return Git
     */
    public function setBin($bin): Git
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * Sets the Git repository path
     *
     * @return Git
     * @var string $directory
     *
     */
    public function setRepository(string $directory): Git
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Sets the Git repository path
     *
     * @return Git
     * @var string $directory
     *
     */
    public function setRepoDir(string $directory): Git
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Returns version number
     *
     * @return string
     * @throws GitException
     */
    public function getVersion(): string
    {
        $builder = $this->getCommandBuilder()->add('--version');

        return $builder->run(true);
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     *
     * @return Git
     */
    public function setTimeout(int $timeout): Git
    {
        $this->timeout = $timeout;
        return $this;
    }
}
