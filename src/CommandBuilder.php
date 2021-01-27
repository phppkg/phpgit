<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit;

use PhpGit\Exception\GitException;
use Symfony\Component\Process\Process;
use function getenv;

/**
 * Class CommandBuilder
 *
 * @package PhpGit
 */
class CommandBuilder
{
    /** @var string */
    private $bin;

    /** @var string */
    private $workDir = '';

    /** @var string git command. eg: clone, fetch */
    private $command;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @var array
     */
    private $options = [];

    /**
     * @param string $command
     *
     * @return static
     */
    public static function create(string $command = ''): self
    {
        return new self($command);
    }

    /**
     * Class constructor.
     *
     * @param string $command
     */
    public function __construct(string $command = '')
    {
        $this->command = $command;
    }

    /**
     * @return string
     */
    public function run(): string
    {
        $process = $this->getProcess();
        $process->run();

        if (!$process->isSuccessful()) {
            throw new GitException($process->getErrorOutput(), $process->getExitCode(), $process->getCommandLine());
        }

        return $process->getOutput();
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        $isWindows = defined('PHP_WINDOWS_VERSION_BUILD');
        $options   = array_merge([
            'environment_variables' => $isWindows ? ['PATH' => getenv('PATH')] : [],
            'process_timeout'       => 3600,
        ], $this->options);

        $cmdWithArgs = array_merge([$this->bin, $this->command], $this->args);

        $process = new Process($cmdWithArgs, $this->workDir ?: null);
        $process->setEnv($options['environment_variables']);
        $process->setTimeout($options['process_timeout']);
        $process->setIdleTimeout($options['process_timeout']);

        return $process;
    }

    /**
     * @param string $arg add option or argument
     *
     * @return $this
     */
    public function add(string $arg): self
    {
        if ($this->command) {
            $this->args[] = $arg;
        } else { // first arg is command
            $this->command = $arg;
        }

        return $this;
    }

    /**
     * @param string $bin
     *
     * @return CommandBuilder
     */
    public function setBin(string $bin): self
    {
        $this->bin = $bin;
        return $this;
    }

    /**
     * @param string $workDir
     *
     * @return CommandBuilder
     */
    public function setWorkDir(string $workDir): self
    {
        $this->workDir = $workDir;
        return $this;
    }

    /**
     * @param array $options
     *
     * @return CommandBuilder
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }
}
