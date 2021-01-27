<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit;

use BadMethodCallException;
use PhpGit\Exception\GitException;
use RuntimeException;
use Symfony\Component\Process\Process;
use function array_merge;
use function defined;

/**
 * # PhpGit
 *
 * [![Latest Unstable Version](https://poser.pugx.org/ulue/phpgit/v/unstable.png)](https://packagist.org/packages/ulue/phpgit)
 * [![Coverage Status](https://coveralls.io/repos/ulue/phpgit/badge.png)](https://coveralls.io/r/ulue/phpgit)
 *
 * PhpGit - A Git wrapper for PHP7.1+
 *
 * ## Requirements
 * ------------
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
 *         "ulue/phpgit": "dev-master"
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
 * $git->clone('https://github.com/ulue/phpgit.git', '/path/to/repo');
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
 * @method add(string $file, $options = [])                                 Add file contents to the index
 * @method archive(string $file, $tree = null, $path = null, $options = []) Create an archive of files from a named tree
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
 * @method remote()                                                         Returns an array of existing remotes
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
    // all supported git commands
    public const COMMANDS = [
        'add'      => Command\Add::class,
        'archive'  => Command\Archive::class,
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

    /** @var string The git repo dir path. */
    private $directory;

    /**
     * @var AbstractCommand[]
     */
    private $commands = [];

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
     * This method is used to create a process object.
     *
     * @param string $command
     * @param array  $args
     * @param array  $options
     *
     * @return Process
     */
    public static function newProcess(string $command, array $args = [], array $options = []): Process
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_BUILD');
        $options   = array_merge([
            'env_vars' => $isWindows ? ['PATH' => getenv('PATH')] : [],
            'command'  => 'git',
            'work_dir' => null,
            'timeout'  => 3600,
        ], $options);

        $cmdWithArgs = array_merge([$options['command'], $command], $args);

        $process = new Process($cmdWithArgs, $options['work_dir']);
        $process->setEnv($options['env_vars']);
        $process->setTimeout($options['timeout']);
        $process->setIdleTimeout($options['timeout']);

        return $process;
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
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, $value): void
    {
        throw new RuntimeException('unsupported set the property ' . $name);
    }

    /**
     * @param string $name
     *
     * @return AbstractCommand
     */
    public function __get(string $name)
    {
        // lazy load command
        if (isset(self::COMMANDS[$name])) {
            return $this->initCommand($name);
        }

        throw new BadMethodCallException(sprintf('Access an undefined property PhpGit\Git->%s', $name));
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        return isset(self::COMMANDS[$name]);
    }

    /**
     * Quick calls sub-commands
     *
     * @param string $name      The name of a property
     * @param array  $arguments An array of arguments
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        if (isset(self::COMMANDS[$name])) {
            // lazy load command
            $cmd = $this->initCommand($name);

            // has __invoke() method
            if (is_callable($cmd)) {
                return $cmd(...$arguments);
            }
        }

        throw new BadMethodCallException(sprintf('Call to undefined method PhpGit\Git::%s()', $name));
    }

    /**
     * @param string $name
     *
     * @return AbstractCommand
     */
    private function initCommand(string $name): AbstractCommand
    {
        // lazy load command
        if (!isset($this->commands[$name])) {
            $class = self::COMMANDS[$name];
            // save
            $this->commands[$name] = new $class($this);
        }

        return $this->commands[$name];
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
     * @return mixed
     * @throws GitException
     */
    public function getVersion()
    {
        $builder = $this->getCommandBuilder()->add('--version');

        return $builder->run();
    }

    /**
     * Returns an instance of ProcessBuilder
     *
     * @param string $command
     *
     * @return CommandBuilder
     */
    public function getCommandBuilder(string $command = ''): CommandBuilder
    {
        return CommandBuilder::create($command)
            ->setBin($this->bin)
            ->setWorkDir($this->directory);
    }

    /**
     * Executes a process
     *
     * @param Process $process The process to run
     *
     * @return mixed
     * @throws Exception\GitException
     */
    public function run(Process $process)
    {
        $process->run();

        if (!$process->isSuccessful()) {
            throw new GitException($process->getErrorOutput(), $process->getExitCode(), $process->getCommandLine());
        }

        return $process->getOutput();
    }

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->directory;
    }
}
