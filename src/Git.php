<?php

namespace PhpGit;

use PhpGit\Exception\GitException;
use Symfony\Component\Process\Process;
use function array_merge;
use function defined;

/**
 * PhpGit - A Git wrapper for PHP5.3+
 * ==================================
 *
 * [![Latest Unstable Version](https://poser.pugx.org/kzykhys/git/v/unstable.png)](https://packagist.org/packages/kzykhys/git)
 * [![Build Status](https://travis-ci.org/kzykhys/PhpGit.png?branch=master)](https://travis-ci.org/kzykhys/PhpGit)
 * [![Coverage Status](https://coveralls.io/repos/kzykhys/PhpGit/badge.png)](https://coveralls.io/r/kzykhys/PhpGit)
 * [![SensioLabsInsight](https://insight.sensiolabs.com/projects/04f10b57-a113-47ad-8dda-9a6dacbb079f/mini.png)](https://insight.sensiolabs.com/projects/04f10b57-a113-47ad-8dda-9a6dacbb079f)
 *
 * Requirements
 * ------------
 *
 * * PHP5.3
 * * Git
 *
 * Installation
 * ------------
 *
 * Update your composer.json and run `composer update`
 *
 * ``` json
 * {
 *     "require": {
 *         "kzykhys/git": "dev-master"
 *     }
 * }
 * ```
 *
 * Basic Usage
 * -----------
 *
 * ``` php
 * <?php
 *
 * require __DIR__ . '/vendor/autoload.php';
 *
 * $git = new PhpGit\Git();
 * $git->clone('https://github.com/kzykhys/PhpGit.git', '/path/to/repo');
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
 * @method add($file, $options = array())                           Add file contents to the index
 * @method archive($file, $tree = null, $path = null, $options = array()) Create an archive of files from a named tree
 * @method branch($options = array())                               List both remote-tracking branches and local branches
 * @method checkout($branch, $options = array())                    Checkout a branch or paths to the working tree
 * @method clone($repository, $path = null, $options = array())     Clone a repository into a new directory
 * @method commit($message = '', $options = array())                Record changes to the repository
 * @method config($options = array())                               List all variables set in config file
 * @method describe($committish = null, $options = array())         Returns the most recent tag that is reachable from a commit
 * @method fetch($repository, $refspec = null, $options = array())  Fetches named heads or tags from one or more other repositories
 * @method init($path, $options = array())                          Create an empty git repository or reinitialize an existing one
 * @method log($path = null, $options = array())                    Returns the commit logs
 * @method merge($commit, $message = null, $options = array())      Incorporates changes from the named commits into the current branch
 * @method mv($source, $destination, $options = array())            Move or rename a file, a directory, or a symlink
 * @method pull($repository = null, $refspec = null, $options = array()) Fetch from and merge with another repository or a local branch
 * @method push($repository = null, $refspec = null, $options = array()) Update remote refs along with associated objects
 * @method rebase($upstream = null, $branch = null, $options = array())  Forward-port local commits to the updated upstream head
 * @method remote()                                                 Returns an array of existing remotes
 * @method reset($commit = null, $paths = array())                  Resets the index entries for all <paths> to their state at <commit>
 * @method rm($file, $options = array())                            Remove files from the working tree and from the index
 * @method shortlog($commits = array())                             Summarize 'git log' output
 * @method show($object, $options = array())                        Shows one or more objects (blobs, trees, tags and commits)
 * @method stash()                                                  Save your local modifications to a new stash, and run git reset --hard to revert them
 * @method status($options = array())                               Show the working tree status
 * @method tag()                                                    Returns an array of tags
 * @method tree($branch = 'master', $path = '')                     List the contents of a tree object
 */
class Git
{
    /** @var Command\Add */
    public $add;

    /** @var Command\Archive */
    public $archive;

    /** @var Command\Branch */
    public $branch;

    /** @var Command\Cat */
    public $cat;

    /** @var Command\Checkout */
    public $checkout;

    /** @var Command\GitClone */
    public $clone;

    /** @var Command\Commit */
    public $commit;

    /** @var Command\Config */
    public $config;

    /** @var Command\Describe */
    public $describe;

    // Not implemented yet
    public $diff;

    /** @var Command\Fetch */
    public $fetch;

    /** @var Command\Init */
    public $init;

    /** @var Command\Log */
    public $log;

    /** @var Command\Merge */
    public $merge;

    /** @var Command\Mv */
    public $mv;

    /** @var Command\Pull */
    public $pull;

    /** @var Command\Push */
    public $push;

    /** @var Command\Rebase */
    public $rebase;

    /** @var Command\Remote */
    public $remote;

    /** @var Command\Reset */
    public $reset;

    /** @var Command\Rm */
    public $rm;

    /** @var Command\Shortlog */
    public $shortlog;

    /** @var Command\Show */
    public $show;

    /** @var Command\Stash */
    public $stash;

    /** @var Command\Status */
    public $status;

    /** @var Command\Tag */
    public $tag;

    /** @var Command\Tree */
    public $tree;

    /** @var string  */
    private $bin = 'git';

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

        $this->add      = new Command\Add($this);
        $this->archive  = new Command\Archive($this);
        $this->branch   = new Command\Branch($this);
        $this->cat      = new Command\Cat($this);
        $this->checkout = new Command\Checkout($this);
        $this->clone    = new Command\GitClone($this);
        $this->commit   = new Command\Commit($this);
        $this->config   = new Command\Config($this);
        $this->describe = new Command\Describe($this);
        $this->fetch    = new Command\Fetch($this);
        $this->init     = new Command\Init($this);
        $this->log      = new Command\Log($this);
        $this->merge    = new Command\Merge($this);
        $this->mv       = new Command\Mv($this);
        $this->pull     = new Command\Pull($this);
        $this->push     = new Command\Push($this);
        $this->rebase   = new Command\Rebase($this);
        $this->remote   = new Command\Remote($this);
        $this->reset    = new Command\Reset($this);
        $this->rm       = new Command\Rm($this);
        $this->shortlog = new Command\Shortlog($this);
        $this->show     = new Command\Show($this);
        $this->stash    = new Command\Stash($this);
        $this->status   = new Command\Status($this);
        $this->tag      = new Command\Tag($this);
        $this->tree     = new Command\Tree($this);
    }

    /**
     * Calls sub-commands
     *
     * @param string $name      The name of a property
     * @param array  $arguments An array of arguments
     *
     * @throws \BadMethodCallException
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        if (isset($this->{$name}) && is_callable($this->{$name})) {
            return call_user_func_array($this->{$name}, $arguments);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method PhpGit\Git::%s()', $name));
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
     * @var string $directory
     *
     * @return Git
     */
    public function setRepository(string $directory): Git
    {
        $this->directory = $directory;

        return $this;
    }

    /**
     * Sets the Git repository path
     *
     * @var string $directory
     *
     * @return Git
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
        $process = $this->getProcessBuilder()
            ->add('--version')
            ->getProcess();

        return $this->run($process);
    }

    /**
     * Returns an instance of ProcessBuilder
     *
     * @param string $command
     *
     * @return ProcessBuilder
     */
    public function getProcessBuilder(string $command = ''): ProcessBuilder
    {
        return ProcessBuilder::create($command)
            ->setBin($this->bin)
            ->setWorkDir($this->directory);
    }

    /**
     * Executes a process
     *
     * @param Process $process The process to run
     *
     * @throws Exception\GitException
     * @return mixed
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
