<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
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
    public function __invoke(array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('--list')->add('--null');

        $this->addFlags($builder, $options, ['global', 'system']);

        $config = [];
        $output = $this->run($builder->getProcess());
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
    public function set($name, $value, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['global', 'system']);

        $builder->add($name)->add($value);

        $process = $builder->getProcess();
        $this->run($process);

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
    public function add($name, $value, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['global', 'system']);

        $builder->add('--add')->add($name)->add($value);
        $process = $builder->getProcess();
        $this->run($process);

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
