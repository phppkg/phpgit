<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
     *@throws GitException
     */
    public function __invoke($branch, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('checkout');

        $this->addFlags($builder, $options, array('force', 'merge'));

        $builder->add($branch);
        $this->git->run($builder->getProcess());

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
     * @param string $startPoint The name of a commit at which to start the new branch
     * @param array  $options    [optional] An array of options {@see Checkout::setDefaultOptions}
     *
     * @return bool
     *@throws GitException
     */
    public function create($branch, $startPoint = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('checkout')
            ->add('-b');

        $this->addFlags($builder, $options, array('force', 'merge'));

        $builder->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * Create a new orphan branch, named <new_branch>, started from <start_point> and switch to it
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
     * @param string $startPoint [optional] The name of a commit at which to start the new branch
     * @param array  $options    [optional] An array of options {@see Checkout::setDefaultOptions}
     *
     * @return bool
     *@throws GitException
     */
    public function orphan($branch, $startPoint = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('checkout');

        $this->addFlags($builder, $options, array('force', 'merge'));

        $builder->add('--orphan')->add($branch);

        if ($startPoint) {
            $builder->add($startPoint);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **force** (_boolean_) Proceed even if the index or the working tree differs from HEAD
     * - **merge** (_boolean_) Merges local modification
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'force' => false,
            'merge' => false
        ));
    }

}