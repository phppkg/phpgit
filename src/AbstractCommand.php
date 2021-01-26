<?php

namespace PhpGit;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\Options;
use function basename;
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
    public function resolve(array $options = array()): array
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
     * @return ProcessBuilder
     */
    protected function getProcessBuilder(): ProcessBuilder
    {
        return $this->git->getProcessBuilder($this->getCommandName());
    }

    /**
     * Adds boolean options to command arguments
     *
     * @param ProcessBuilder $builder     A ProcessBuilder object
     * @param array          $options     An array of options
     * @param array          $optionNames The names of options to add
     */
    protected function addFlags(ProcessBuilder $builder, array $options = [], array $optionNames = []): void
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
     * @param ProcessBuilder $builder     A ProcessBuilder object
     * @param array          $options     An array of options
     * @param array|null     $optionNames The names of options to add
     */
    protected function addValues(ProcessBuilder $builder, array $options = [], array $optionNames = null): void
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