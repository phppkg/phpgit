<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\Concern\AbstractCommand;
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;

/**
 * Get and set repository or global options - `git config`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Config extends AbstractCommand
{
    /**
     * Returns all variables set in config file
     *
     * ##### Options
     *
     * - **global** (_boolean_) Read or write configuration options for the current user
     * - **system** (_boolean_) Read or write configuration options for all users on the current machine
     *
     * @param array $options [optional] An array of options {@see Config::setDefaultOptions}
     *
     * @return array
     * @throws GitException
     */
    public function __invoke(array $options = []): array
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('--list')->add('--null');

        $this->addFlags($builder, $options, ['global', 'system']);

        $config = [];
        $output = $this->run($builder);
        $lines  = $this->split($output, true);

        foreach ($lines as $line) {
            [$name, $value] = explode("\n", $line, 2);

            if (isset($config[$name])) {
                $config[$name] .= "\n" . $value;
            } else {
                $config[$name] = $value;
            }
        }

        return $config;
    }

    /**
     * Get all config var names
     *
     * ##### Options
     *
     * - **global** (_boolean_) Read or write configuration options for the current user
     * - **system** (_boolean_) Read or write configuration options for all users on the current machine
     *
     * @param array $options
     *
     * @return array
     */
    public function getNames(array $options = []): array
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();
        $this->addFlags($builder, $options, ['global', 'system']);

        $output = $builder->add('--list', '--name-only')->run();
        return $this->split($output, true);
    }

    /**
     * Get config value by name
     *
     * #### Example
     *
     * ```php
     * $git->config->get('user.name')
     * $git->config->get('user.email')
     * ```
     *
     * ##### Options
     *
     * - **global** (_boolean_) Read or write configuration options for the current user
     * - **system** (_boolean_) Read or write configuration options for all users on the current machine
     *
     * @param string $key config name.
     * @param array  $options
     *
     * @return string
     */
    public function get(string $key, array $options = []): string
    {
        // eg: git config --global --get user.name
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();
        $this->addFlags($builder, $options, ['global', 'system']);

        return $builder->add('--get', $key)->run(true);
    }

    /**
     * Set an option
     *
     * ##### Options
     *
     * - **global** (_boolean_) Read or write configuration options for the current user
     * - **system** (_boolean_) Read or write configuration options for all users on the current machine
     *
     * @param string $name    The name of the option
     * @param string $value   The value to set
     * @param array  $options [optional] An array of options {@see Config::setDefaultOptions}
     *
     * @return bool
     * @throws GitException
     */
    public function set(string $name, string $value, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['global', 'system']);

        $builder->add($name)->add($value);

        $this->run($builder);

        return true;
    }

    /**
     * Adds a new line to the option without altering any existing values
     *
     * ##### Options
     *
     * - **global** (_boolean_) Read or write configuration options for the current user
     * - **system** (_boolean_) Read or write configuration options for all users on the current machine
     *
     * @param string $name    The name of the option
     * @param string $value   The value to add
     * @param array  $options [optional] An array of options {@see Config::setDefaultOptions}
     *
     * @return bool
     * @throws GitException
     */
    public function add(string $name, string $value, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['global', 'system']);

        $builder->add('--add')->add($name)->add($value);

        $this->run($builder);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **global** (_boolean_) Read or write configuration options for the current user
     * - **system** (_boolean_) Read or write configuration options for all users on the current machine
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'global' => false,
            'system' => false,
        ]);
    }
}
