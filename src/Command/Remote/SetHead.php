<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit\Command\Remote;

use PhpGit\Concern\AbstractCommand;

/**
 * Sets or deletes the default branch (i.e. the target of the symbolic-ref refs/remotes/<name>/HEAD) for the named remote
 *
 * @author Kazuyuki Hayashi
 */
class SetHead extends AbstractCommand
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
     * $git->remote->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
     * $git->remote->head('origin');
     * ```
     *
     * @param string $name   The remote name
     * @param null   $branch [optional] The symbolic-ref to set
     *
     * @return bool
     */
    public function __invoke($name, $branch = null)
    {
        return $this->set($name, $branch);
    }

    /**
     * Sets the default branch for the named remote
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
     * $git->remote->head->set('origin');
     * ```
     *
     * @param string $name   The remote name
     * @param string $branch [optional] The symbolic-ref to set
     *
     * @return bool
     */
    public function set(string $name, string $branch = ''): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('set-head')
            ->add($name);

        if ($branch) {
            $builder->add($branch);
        }

        $this->run($builder);

        return true;
    }

    /**
     * Deletes the default branch for the named remote
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
     * $git->remote->head->delete('origin');
     * ```
     *
     * @param string $name The remote name
     *
     * @return bool
     */
    public function delete(string $name): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('set-head')
            ->add($name)
            ->add('-d');

        $this->run($builder);

        return true;
    }

    /**
     * Determine the default branch by querying remote
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->remote->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
     * $git->remote->head->remote('origin');
     * ```
     *
     * @param string $name The remote name
     *
     * @return bool
     */
    public function remote(string $name): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('set-head')
            ->add($name)
            ->add('-a');

        $this->run($builder);

        return true;
    }
}
