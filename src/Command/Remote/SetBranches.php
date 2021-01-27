<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Command\Remote;

use PhpGit\AbstractCommand;

/**
 * Changes the list of branches tracked by the named remote
 *
 * @author Kazuyuki Hayashi
 */
class SetBranches extends AbstractCommand
{
    public function getCommandName(): string
    {
        return 'remote';
    }

    /**
     * Alias of set()
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->branches('origin', array('master', 'develop'));
     * ```
     *
     * @param string $name     The remote name
     * @param array  $branches The names of the tracked branch
     *
     * @return bool
     */
    public function __invoke($name, array $branches)
    {
        return $this->set($name, $branches);
    }

    /**
     * Changes the list of branches tracked by the named remote
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->branches->set('origin', array('master', 'develop'));
     * ```
     *
     * @param string $name     The remote name
     * @param array  $branches The names of the tracked branch
     *
     * @return bool
     */
    public function set($name, array $branches): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('set-branches')
            ->add($name);

        foreach ($branches as $branch) {
            $builder->add($branch);
        }

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * Adds to the list of branches tracked by the named remote
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
     * $git->remote->branches->add('origin', array('master', 'develop'));
     * ```
     *
     * @param string $name     The remote name
     * @param array  $branches The names of the tracked branch
     *
     * @return bool
     */
    public function add($name, array $branches): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('set-branches')
            ->add($name)
            ->add('--add');

        foreach ($branches as $branch) {
            $builder->add($branch);
        }

        $this->run($builder->getProcess());

        return true;
    }
}
