<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use InvalidArgumentException;
use PhpGit\Concern\AbstractCommand;
use PhpGit\Exception\GitException;
use Traversable;

/**
 * Reset current HEAD to the specified state - `git reset`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Reset extends AbstractCommand
{
    /**
     * Resets the index entries for all **$paths** to their state at **$commit**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset();
     * ```
     *
     * @param string|array|Traversable $paths  The paths to reset
     * @param null                     $commit The commit
     *
     * @return bool
     */
    public function __invoke($paths, $commit = null): bool
    {
        $builder = $this->getCommandBuilder();

        if ($commit) {
            $builder->add($commit)->add('--');
        }

        if (!is_array($paths) && !($paths instanceof Traversable)) {
            $paths = [$paths];
        }

        foreach ($paths as $path) {
            $builder->add($path);
        }

        try {
            $this->run($builder);
        } catch (GitException $e) {
            // Confirm exit code
        }

        return true;
    }

    /**
     * Resets the current branch head to **$commit**
     *
     * Does not touch the index file nor the working tree at all (but resets the head to **$commit**,
     * just like all modes do).
     * This leaves all your changed files "Changes to be committed", as git status would put it.
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset->soft();
     * ```
     *
     * @param null $commit The commit
     *
     * @return bool
     */
    public function soft($commit = null): bool
    {
        return $this->mode('soft', $commit);
    }

    /**
     * Resets the current branch head to **$commit**
     *
     * Resets the index but not the working tree (i.e., the changed files are preserved but not marked for commit)
     * and reports what has not been updated. This is the default action.
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset->mixed();
     * ```
     *
     * @param null $commit The commit
     *
     * @return bool
     */
    public function mixed($commit = null): bool
    {
        return $this->mode('mixed', $commit);
    }

    /**
     * Resets the current branch head to **$commit**
     *
     * Resets the index and working tree. Any changes to tracked files in the working tree since **$commit** are discarded
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset->hard();
     * ```
     *
     * @param null $commit The commit
     *
     * @return bool
     */
    public function hard($commit = null): bool
    {
        return $this->mode('hard', $commit);
    }

    /**
     * Resets the current branch head to **$commit**
     *
     * Resets the index and updates the files in the working tree that are different between **$commit** and HEAD,
     * but keeps those which are different between the index and working tree
     * (i.e. which have changes which have not been added). If a file that is different between **$commit** and
     * the index has unstaged changes, reset is aborted
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset->merge();
     * ```
     *
     * @param null $commit The commit
     *
     * @return bool
     */
    public function merge($commit = null): bool
    {
        return $this->mode('merge', $commit);
    }

    /**
     * Resets the current branch head to **$commit**
     *
     * Resets index entries and updates files in the working tree that are different between **$commit** and HEAD.
     * If a file that is different between **$commit** and HEAD has local changes, reset is aborted.
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset->keep();
     * ```
     *
     * @param null $commit The commit
     *
     * @return bool
     */
    public function keep($commit = null): bool
    {
        return $this->mode('keep', $commit);
    }

    /**
     * Resets the current branch head to **$commit**
     *
     * Possibly updates the index (resetting it to the tree of **$commit**) and the working tree depending on **$mode**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->reset->mode('hard');
     * ```
     *
     * @param string $mode   --<mode>
     * @param string|null   $commit The commit
     *
     * @return bool
     */
    public function mode(string $mode, string $commit = null): bool
    {
        if (!in_array($mode, ['soft', 'mixed', 'hard', 'merge', 'keep'])) {
            throw new InvalidArgumentException('$mode must be one of the following: soft, mixed, hard, merge, keep');
        }

        $builder = $this->getCommandBuilder('--' . $mode);

        if ($commit) {
            $builder->add($commit);
        }

        $this->run($builder);

        return true;
    }
}
