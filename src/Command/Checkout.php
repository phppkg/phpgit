<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
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
 * Checkout a branch or paths to the working tree - `git checkout`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Checkout extends AbstractCommand
{
    /**
     * Switches branches by updating the index, working tree, and HEAD to reflect the specified branch or commit
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->checkout('develop');
     * ```
     *
     * ##### Options
     *
     * - **force** (_boolean_) Proceed even if the index or the working tree differs from HEAD
     * - **merge** (_boolean_) Merges local modification
     *
     * @param string $branch  Branch to checkout
     * @param array  $options [optional] An array of options {@see Checkout::setDefaultOptions}
     *
     * @return bool
     * @throws GitException
     */
    public function __invoke($branch, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['force', 'merge']);

        $builder->add($branch);
        $this->run($builder->getProcess());

        return true;
    }

    /**
     * Create a new branch and checkout
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->checkout->create('patch-1');
     * $git->checkout->create('patch-2', 'develop');
     * ```
     *
     * ##### Options
     *
     * - **force** (_boolean_) Proceed even if the index or the working tree differs from HEAD
     *
     * @param string $branch     Branch to checkout
     * @param null   $startPoint The name of a commit at which to start the new branch
     * @param array  $options    [optional] An array of options {@see Checkout::setDefaultOptions}
     *
     * @return bool
     */
    public function create($branch, $startPoint = null, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder('-b');

        $this->addFlags($builder, $options, ['force', 'merge']);

        $builder->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * Create a new orphan branch, named `new_branch`, started from `start_point` and switch to it
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->checkout->orphan('gh-pages');
     * ```
     *
     * ##### Options
     *
     * - **force** (_boolean_) Proceed even if the index or the working tree differs from HEAD
     *
     * @param string $branch     Branch to checkout
     * @param null   $startPoint [optional] The name of a commit at which to start the new branch
     * @param array  $options    [optional] An array of options {@see Checkout::setDefaultOptions}
     *
     * @return bool
     */
    public function orphan($branch, $startPoint = null, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['force', 'merge']);

        $builder->add('--orphan')->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **force** (_boolean_) Proceed even if the index or the working tree differs from HEAD
     * - **merge** (_boolean_) Merges local modification
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'force' => false,
            'merge' => false
        ]);
    }
}
