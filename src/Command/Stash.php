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

/**
 * Stash the changes in a dirty working directory away - `git stash`
 *
 * @author Kazuyuki Hayashi
 */
class Stash extends AbstractCommand
{
    /**
     * Save your local modifications to a new stash, and run git reset --hard to revert them
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash();
     * ```
     *
     * @return bool
     */
    public function __invoke(): bool
    {
        $builder = $this->getCommandBuilder();

        $this->run($builder);

        return true;
    }

    /**
     * Save your local modifications to a new stash, and run git reset --hard to revert them.
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash->save('My stash');
     * ```
     *
     * @param string $message [optional] The description along with the stashed state
     * @param array  $options [optional] An array of options {@see Stash::setDefaultOptions}
     *
     * @return bool
     */
    public function save(string $message = '', array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('save');

        $builder->add($message);

        $this->run($builder);

        return true;
    }

    /**
     * Returns the stashes that you currently have
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $stashes = $git->stash->lists();
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     0 => ['branch' => 'master', 'message' => '0e2f473 Fixes README.md'],
     *     1 => ['branch' => 'master', 'message' => 'ce1ddde Initial commit'],
     * ]
     * ```
     *
     * @param array $options [optional] An array of options {@see Stash::setDefaultOptions}
     *
     * @return array
     */
    public function lists(array $options = []): array
    {
        $builder = $this->getCommandBuilder()->add('list');

        $output = $this->run($builder);
        $lines  = $this->split($output);
        $list   = [];

        foreach ($lines as $line) {
            if (preg_match('/stash@{(\d+)}:.* [Oo]n (.*): (.*)/', $line, $matches)) {
                $list[$matches[1]] = [
                    'branch'  => $matches[2],
                    'message' => $matches[3]
                ];
            }
        }

        return $list;
    }

    /**
     * Show the changes recorded in the stash as a diff between the stashed state and its original parent
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * echo $git->stash->show('stash@{0}');
     * ```
     *
     * ##### Output Example
     *
     * ```
     *  REAMDE.md |    2 +-
     *  1 files changed, 1 insertions(+), 1 deletions(-)
     * ```
     *
     * @param null $stash The stash to show
     *
     * @return string
     */
    public function show($stash = null): string
    {
        $builder = $this->getCommandBuilder()->add('show');

        if ($stash) {
            $builder->add($stash);
        }

        return $this->run($builder);
    }

    /**
     * Remove a single stashed state from the stash list
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash->drop('stash@{0}');
     * ```
     *
     * @param string|null $stash The stash to drop
     *
     * @return string
     */
    public function drop(string $stash = null): string
    {
        $builder = $this->getCommandBuilder()->add('drop');

        if ($stash) {
            $builder->add($stash);
        }

        return $this->run($builder);
    }

    /**
     * Remove a single stashed state from the stash list and apply it on top of the current working tree state
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash->pop('stash@{0}');
     * ```
     *
     * @param string $stash   The stash to pop
     * @param array  $options [optional] An array of options {@see Stash::setDefaultOptions}
     *
     * @return bool
     */
    public function pop(string $stash = '', array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('pop');

        $this->addFlags($builder, $options, ['index']);

        if ($stash) {
            $builder->add($stash);
        }

        $this->run($builder);

        return true;
    }

    /**
     * Like pop, but do not remove the state from the stash list
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash->apply('stash@{0}');
     * ```
     *
     * @param string $stash   The stash to apply
     * @param array  $options [optional] An array of options {@see Stash::setDefaultOptions}
     *
     * @return bool
     */
    public function apply(string $stash = '', array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('apply');

        $this->addFlags($builder, $options, ['index']);

        if ($stash) {
            $builder->add($stash);
        }

        $this->run($builder);

        return true;
    }

    /**
     * Creates and checks out a new branch named <branchname> starting from the commit at which the <stash> was originally created, applies the changes recorded in <stash> to the new working tree and index
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash->branch('hotfix', 'stash@{0}');
     * ```
     *
     * @param string      $name  The name of the branch
     * @param string $stash The stash
     *
     * @return bool
     */
    public function branch(string $name, string $stash = ''): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('branch')
            ->add($name);

        if ($stash) {
            $builder->add($stash);
        }

        $this->run($builder);

        return true;
    }

    /**
     * Remove all the stashed states
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->stash->clear();
     * ```
     *
     * @return bool
     */
    public function clear(): bool
    {
        $builder = $this->getCommandBuilder()
            ->add('clear');

        $this->run($builder);

        return true;
    }

    /**
     * Create a stash (which is a regular commit object) and return its object name, without storing it anywhere in the ref namespace
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $commit = $git->stash->create();
     * ```
     *
     * **Output Example**
     *
     * ```
     * 877316ea6f95c43b7ccc2c2a362eeedfa78b597d
     * ```
     *
     * @return string
     */
    public function create(): string
    {
        $builder = $this->getCommandBuilder()->add('create');

        return $this->run($builder);
    }
}
