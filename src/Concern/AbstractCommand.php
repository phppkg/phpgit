<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Concern;

use PhpGit\CommandBuilder;
use PhpGit\Git;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function basename;
use function preg_split;
use function rtrim;
use function str_replace;
use function strtolower;
use const PREG_SPLIT_NO_EMPTY;

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

        return strtolower(basename($fullName));
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
     * @param Options $resolver
     * @param array   $options
     */
    public function batchSetAllowedTypes(Options $resolver, array $options): void
    {
        foreach ($options as $option => $allowedTypes) {
            $resolver->setAllowedTypes($option, $allowedTypes);
        }
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
        // nothing ...
    }

    /**
     * Split string by new line or null(\0)
     *
     * @param string $input   The string to split
     * @param bool   $useNull True to split by new line, otherwise null
     *
     * @return array
     */
    protected function split(string $input, $useNull = false): array
    {
        if ($useNull) {
            $pattern = '/\0/';
        } else {
            $pattern = '/\r?\n/';
        }

        return preg_split($pattern, rtrim($input), -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param string ...$args
     *
     * @return CommandBuilder
     */
    protected function getCommandBuilder(string ...$args): CommandBuilder
    {
        $cmd = $this->getCommandName();

        return $this->git->getCommandBuilder($cmd, ...$args);
    }

    /**
     * Executes a process
     *
     * @param CommandBuilder $builder
     *
     * @return string
     */
    public function run(CommandBuilder $builder): string
    {
        return $builder->run();
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

    /**
     * @return Git
     */
    public function getGit(): Git
    {
        return $this->git;
    }
}
