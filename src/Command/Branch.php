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
use PhpGit\GitUtil;
use Symfony\Component\OptionsResolver\Options;

/**
 * List, create, or delete branches - `git branch`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Branch extends AbstractCommand
{
    /**
     * Returns an array of both remote-tracking branches and local branches
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $branches = $git->branch();
     * ```
     *
     * ##### Output Example
     *
     * ```
     * [
     *     'master' => ['current' => true, 'name' => 'master', 'hash' => 'bf231bb', 'title' => 'Initial Commit'],
     *     'origin/master' => ['current' => false, 'name' => 'origin/master', 'alias' => 'remotes/origin/master']
     * ]
     * ```
     *
     * ##### Options
     *
     * - **all**     (_boolean_) List both remote-tracking branches and local branches
     * - **remotes** (_boolean_) List the remote-tracking branches
     *
     * @param array $options [optional] An array of options {@see Branch::setDefaultOptions}
     *
     * @return array
     * @throws GitException
     */
    public function __invoke(array $options = []): array
    {
        return $this->getList($options);
    }

    /**
     * @param array $options
     *
     * @return array
     */
    public function getList(array $options = []): array
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder('-v', '--abbrev=7');

        if ($options['remotes']) {
            $builder->add('--remotes');
        }

        if ($options['all']) {
            $builder->add('--all');
        }

        // $process = $builder->getProcess();
        $output = $this->run($builder);

        // Output:
        // * master                d35f850 up: Add option tags to fetch command
        //   remotes/origin/HEAD   -> origin/master
        //   remotes/origin/master d35f850 up: Add option tags to fetch command
        $lines = preg_split('/\r?\n/', rtrim($output), -1, PREG_SPLIT_NO_EMPTY);

        $branches = [];
        foreach ($lines as $line) {
            $branch = GitUtil::parseBranchLine($line);

            $branches[$branch['name']] = $branch;
        }

        return $branches;
    }

    /**
     * Creates a new branch head named **$branch** which points to the current HEAD, or **$startPoint** if given
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->branch->create('bugfix');              // from current HEAD
     * $git->branch->create('patch-1', 'a092bf7s'); // from commit
     * $git->branch->create('1.0.x-fix', 'v1.0.2'); // from tag
     * ```
     *
     * ##### Options
     *
     * - **force**   (_boolean_) Reset **$branch**  to **$startPoint** if **$branch** exists already
     *
     * @param string $branch      The name of the branch to create
     * @param null   $startPoint  [optional] The new branch head will point to this commit.
     *                            It may be given as a branch name, a commit-id, or a tag.
     *                            If this option is omitted, the current HEAD will be used instead.
     * @param array  $options     [optional] An array of options {@see Branch::setDefaultOptions}
     *
     * @return bool
     */
    public function create(string $branch, $startPoint = null, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        if ($options['force']) {
            $builder->add('-f');
        }

        $builder->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->run($builder);

        return true;
    }

    /**
     * Move/rename a branch and the corresponding reflog
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->branch->move('bugfix', '2.0');
     * ```
     *
     * ##### Options
     *
     * - **force**   (_boolean_) Move/rename a branch even if the new branch name already exists
     *
     * @param string $branch    The name of an existing branch to rename
     * @param string $newBranch The new name for an existing branch
     * @param array  $options   [optional] An array of options {@see Branch::setDefaultOptions}
     *
     * @return bool
     * @throws GitException
     */
    public function move(string $branch, string $newBranch, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        if ($options['force']) {
            $builder->add('-M');
        } else {
            $builder->add('-m');
        }

        $builder->add($branch)->add($newBranch);
        $this->run($builder);

        return true;
    }

    /**
     * Delete a branch
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->branch->delete('2.0');
     * ```
     *
     * The branch must be fully merged in its upstream branch, or in HEAD if no upstream was set with --track or --set-upstream.
     *
     * ##### Options
     *
     * - **force**   (_boolean_) Delete a branch irrespective of its merged status
     *
     * @param string $branch  The name of the branch to delete
     * @param array  $options [optional] An array of options {@see Branch::setDefaultOptions}
     *
     * @return bool
     * @throws GitException
     */
    public function delete(string $branch, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        if ($options['force']) {
            $builder->add('-D');
        } else {
            $builder->add('-d');
        }

        $builder->add($branch);
        $this->run($builder);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **force**   (_boolean_) Reset <branchname> to <startpoint> if <branchname> exists already
     * - **all**     (_boolean_) List both remote-tracking branches and local branches
     * - **remotes** (_boolean_) List or delete (if used with delete()) the remote-tracking branches
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'force'   => false,
            'all'     => false,
            'remotes' => false,
        ]);
    }
}
