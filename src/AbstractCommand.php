<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit;

use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Process\Process;
use function basename;
use function preg_split;
use function rtrim;
use function str_replace;

/**
 * Base class for git commands
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
abstract class AbstractCommand
{
    /**
     * @var Git
     */
    protected $git;

    /**
     * @param Git $git
     */
    public function __construct(Git $git)
    {
        $this->git = $git;
    }

    /**
     * @return string
     */
    public function getCommandName(): string
    {
        $fullName = str_replace('\\', '/', static::class);

        return basename($fullName);
    }

    /**
     * Returns the combination of the default and the passed options
     *
     * @param array $options An array of options
     *
     * @return array
     */
    public function resolve(array $options = []): array
    {
        $resolver = new OptionsResolver();
        $this->setDefaultOptions($resolver);

        return $resolver->resolve($options);
    }

    /**
     * Sets the default options
     *
     * @param Options $resolver The resolver for the options
     *
     * @codeCoverageIgnore
     */
    public function setDefaultOptions(Options $resolver): void
    {
    }

    /**
     * Split string by new line or null(\0)
     *
     * @param string $input   The string to split
     * @param bool   $useNull True to split by new line, otherwise null
     *
     * @return array
     */
    protected function split($input, $useNull = false): array
    {
        if ($useNull) {
            $pattern = '/\0/';
        } else {
            $pattern = '/\r?\n/';
        }

        return preg_split($pattern, rtrim($input), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param mixed ...$args
     *
     * @return CommandBuilder
     */
    protected function getCommandBuilder(...$args): CommandBuilder
    {
        $cmd = $this->getCommandName();

        return $this->git->getCommandBuilder($cmd, ...$args);
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
     * Adds boolean options to command arguments
     *
     * @param CommandBuilder $builder     A ProcessBuilder object
     * @param array          $options     An array of options
     * @param array          $optionNames The names of options to add
     */
    protected function addFlags(CommandBuilder $builder, array $options = [], array $optionNames = []): void
    {
        if ($optionNames) {
            foreach ($optionNames as $name) {
                if (isset($options[$name]) && is_bool($options[$name]) && $options[$name]) {
                    $builder->add('--' . $name);
                }
            }
        } else {
            foreach ($options as $name => $option) {
                if ($option) {
                    $builder->add('--' . $name);
                }
            }
        }
    }

    /**
     * Adds options with values to command arguments
     *
     * @param CommandBuilder $builder     A ProcessBuilder object
     * @param array          $options     An array of options
     * @param array|null     $optionNames The names of options to add
     */
    protected function addValues(CommandBuilder $builder, array $options = [], array $optionNames = null): void
    {
        if ($optionNames) {
            foreach ($optionNames as $name) {
                if (isset($options[$name]) && $options[$name]) {
                    $builder->add('--' . $name . '=' . $options[$name]);
                }
            }
        } else {
            foreach ($options as $name => $option) {
                if ($option) {
                    $builder->add('--' . $name . '=' . $option);
                }
            }
        }
    }
}
