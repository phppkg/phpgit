<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit;

use PhpGit\Exception\GitException;
use Symfony\Component\Process\Process;
use Toolkit\Cli\Color;
use function implode;
use function sprintf;
use function trim;

/**
 * Class CommandBuilder
 *
 * @package PhpGit
 */
class CommandBuilder
{
    /**
     * @var string
     */
    private $bin = 'git';

    /**
     * @var string
     */
    private $workDir = '';

    /**
     * git command. eg: clone, fetch
     *
     * @var string
     */
    private $command;

    /**
     * git command args
     *
     * @var array
     */
    private $args = [];

    /**
     * Process options
     * - timeout
     * - bin_name
     * - env_vars
     *
     * @var array
     */
    private $options = [];

    /**
     * Direct set full command line. eg: `git symbolic-ref --short -q HEAD`
     *
     * NOTICE: if is not empty, will ignore $bin, $command, $args
     *
     * @var string
     */
    private $commandLine = '';

    /**
     * @param string $command
     * @param mixed  ...$args
     *
     * @return static
     */
    public static function new(string $command = '', string ...$args): self
    {
        return new self($command, ...$args);
    }

    /**
     * @param string $command
     * @param mixed  ...$args
     *
     * @return static
     */
    public static function create(string $command = '', string ...$args): self
    {
        return new self($command, ...$args);
    }

    /**
     * Class constructor.
     *
     * @param string $command
     * @param mixed  ...$args
     */
    public function __construct(string $command = '', string ...$args)
    {
        $this->command = $command;
        $this->add(...$args);
    }

    /**
     * @param bool $trimOutput
     *
     * @return string
     */
    public function run(bool $trimOutput = false): string
    {
        $cmdLine = $this->getCommandLine();
        Color::println("> $cmdLine", 'ylw');

        $process = $this->createProcess();

        // start and wait
        $process->run();
        // $process->run(null, ['MY_VAR' => $theValue]]);

        if (!$process->isSuccessful()) {
            throw new GitException($process->getErrorOutput(), $process->getExitCode(), $cmdLine);
        }

        $output = $process->getOutput();

        return $trimOutput ? trim($output) : $output;
    }

    /**
     * @param string ...$args add options or arguments
     *
     * @return $this
     */
    public function add(string ...$args): self
    {
        foreach ($args as $arg) {
            if ($this->command) {
                $this->args[] = $arg;
            } else { // first arg is command
                $this->command = $arg;
            }
        }

        return $this;
    }

    /**
     * Alias of add()
     *
     * @param array $args
     *
     * @return $this
     */
    public function addArgs(...$args): self
    {
        return $this->add(...$args);
    }

    /**
     * @return Process
     */
    public function createProcess(): Process
    {
        $options = $this->options;

        // work_dir and bin_name
        $options['bin_name'] = $this->bin;
        $options['work_dir'] = $this->workDir;
        $options['cmd_line'] = $this->commandLine;

        return GitUtil::newProcess($this->command, $this->args, $options);
    }

    /**
     * @return string
     */
    public function getCommandLine(): string
    {
        if ($this->commandLine) {
            return $this->commandLine;
        }

        $argStr = implode(' ', $this->args);
        // foreach ($this->args as $arg) {
        //
        // }

        return sprintf('%s %s %s', $this->bin, $this->command, $argStr);
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
     * @param int $timeout
     *
     * @return $this
     */
    public function setTimeout(int $timeout): self
    {
        return $this->setOption('timeout', $timeout);
    }

    /**
     * @param string $option
     * @param mixed  $value
     *
     * @return CommandBuilder
     */
    public function setOption(string $option, $value): self
    {
        $this->options[$option] = $value;
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

    /**
     * @param string $cmdLine
     *
     * @return $this
     */
    public function setCommandLine(string $cmdLine): self
    {
        $this->commandLine = $cmdLine;
        return $this;
    }
}
