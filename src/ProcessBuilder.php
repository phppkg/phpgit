<?php declare(strict_types=1);

namespace PhpGit;

use Symfony\Component\Process\Process;
use function getenv;

/**
 * Class ProcessBuilder
 *
 * @package PhpGit
 */
class ProcessBuilder
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
     * @return ProcessBuilder
     */
    public function setBin(string $bin): self
    {
        $this->bin = $bin;
        return $this;
    }

    /**
     * @param string $workDir
     *
     * @return ProcessBuilder
     */
    public function setWorkDir(string $workDir): self
    {
        $this->workDir = $workDir;
        return $this;
    }

    /**
     * @param array $options
     *
     * @return ProcessBuilder
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;
        return $this;
    }
}
