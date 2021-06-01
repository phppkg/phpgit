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
use Toolkit\Stdlib\Str;
use function chdir;
use function implode;
use function sprintf;
use function system;
use function trim;

/**
 * Class CommandBuilder
 *
 * @package PhpGit
 */
class CmdBuilder
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
     * Dry run all commands
     *
     * @var bool
     */
    protected $dryRun = false;

    /**
     * @var bool
     */
    protected $printCmd = true;

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
     * @var int
     */
    protected $code = 0;

    /**
     * @var string
     */
    protected $error = '';

    /**
     * @var string
     */
    private $output = '';

    /**
     * @param string $command
     * @param mixed  ...$args
     *
     * @return static
     */
    public static function new(string $command = '', ...$args): self
    {
        return new self($command, ...$args);
    }

    /**
     * @param string $command
     * @param mixed  ...$args
     *
     * @return static
     */
    public static function create(string $command = '', ...$args): self
    {
        return new self($command, ...$args);
    }

    /**
     * Class constructor.
     *
     * @param string $command
     * @param mixed  ...$args
     */
    public function __construct(string $command = '', ...$args)
    {
        $this->command = $command;
        $this->add(...$args);
    }

    /**
     * direct run and not collect returns.
     */
    public function runAndPrint(): void
    {
        $cmdLine = $this->getCommandLine();

        if ($this->printCmd) {
            Color::println("> $cmdLine", 'yellow');
        }

        if ($this->dryRun) {
            $output = 'DRY-RUN: Command execute success';
            Color::println($output, 'cyan');
            return;
        }

        $this->execAndPrint($cmdLine, $this->workDir);
    }

    /**
     * direct print to stdout, not return outputs.
     *
     * @param string $command
     * @param string $workDir
     */
    protected function execAndPrint(string $command, string $workDir): void
    {
        // TODO use Exec::system($command);
        if ($workDir) {
            chdir($workDir);
        }

        $lastLine   = system($command, $exitCode);
        $this->code = $exitCode;

        if ($exitCode !== 0) {
            $this->error = trim($lastLine);
            Color::println("error code $exitCode:\n" . $lastLine, 'red');
        } else {
            echo "\n";
        }
    }

    /**
     * @param bool $trimOutput
     *
     * @return string
     */
    public function run(bool $trimOutput = false): string
    {
        $cmdLine = $this->getCommandLine();
        if ($this->printCmd) {
            Color::println("> $cmdLine", 'ylw');
        }

        $proc = $this->createProcess();

        // start and wait
        $proc->run();
        // $process->run(null, ['MY_VAR' => $theValue]]);

        if (!$proc->isSuccessful()) {
            throw new GitException('GIT error:' . $proc->getErrorOutput(), $proc->getExitCode(), $cmdLine);
        }

        $output = $proc->getOutput();
        $output = $trimOutput ? trim($output) : $output;

        $this->output = $output;
        return $output;
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
     * @param string $format
     * @param mixed  ...$a
     *
     * @return $this
     */
    public function addf(string $format, ...$a): self
    {
        $this->args[] = sprintf($format, ...$a);
        return $this;
    }

    /**
     * @param string|int      $arg
     * @param bool|int|string $cond
     *
     * @return $this
     */
    public function addIf($arg, $cond): self
    {
        if ($cond) {
            $this->args[] = $arg;
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

        $argList = [];
        foreach ($this->args as $arg) {
            $argList[] = Str::shellQuote((string)$arg);
        }

        $argString = implode(' ', $argList);
        return sprintf('%s %s %s', $this->bin, $this->command, $argString);
    }

    /**
     * @param string $bin
     *
     * @return CmdBuilder
     */
    public function setBin(string $bin): self
    {
        $this->bin = $bin;
        return $this;
    }

    /**
     * @param bool $dryRun
     *
     * @return $this
     */
    public function setDryRun(bool $dryRun): self
    {
        $this->dryRun = $dryRun;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDryRun(): bool
    {
        return $this->dryRun;
    }

    /**
     * @param string $workDir
     *
     * @return CmdBuilder
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
     * @return CmdBuilder
     */
    public function setOption(string $option, $value): self
    {
        $this->options[$option] = $value;
        return $this;
    }

    /**
     * @param array $options
     *
     * @return CmdBuilder
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

    /**
     * @return $this
     */
    public function notPrintCmd(): self
    {
        return $this->setPrintCmd(false);
    }

    /**
     * @param bool $printCmd
     *
     * @return $this
     */
    public function setPrintCmd(bool $printCmd): self
    {
        $this->printCmd = $printCmd;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }
}
