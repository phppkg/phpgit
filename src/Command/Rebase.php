<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;

/**
 * Forward-port local commits to the updated upstream head - `git rebase`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Rebase extends AbstractCommand
{

    /**
     * Forward-port local commits to the updated upstream head
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->fetch('origin');
     * $git->rebase('origin/master');
     * ```
     *
     * ##### Options
     *
     * - **onto**          (_string_)  Starting point at which to create the new commits
     * - **no-verify**     (_boolean_) Bypasses the pre-rebase hook
     * - **force-rebase**  (_boolean_) Force the rebase even if the current branch is a descendant of the commit you are rebasing onto
     *
     * @param null  $upstream [optional] Upstream branch to compare against
     * @param null  $branch   [optional] Working branch; defaults to HEAD
     * @param array $options  [optional] An array of options {@see Rebase::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($upstream = null, $branch = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('rebase');

        if ($options['onto']) {
            $builder->add('--onto')->add($options['onto']);
        }

        if ($upstream) {
            $builder->add($upstream);
        }

        if ($branch) {
            $builder->add($branch);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * Restart the rebasing process after having resolved a merge conflict
     *
     * @return bool
     */
    public function continues(): bool
    {
        $builder = $this->git->getProcessBuilder()
            ->add('rebase')
            ->add('--continue');

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * Abort the rebase operation and reset HEAD to the original branch
     *
     * @return bool
     */
    public function abort(): bool
    {
        $builder = $this->git->getProcessBuilder()
            ->add('rebase')
            ->add('--abort');

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * Restart the rebasing process by skipping the current patch
     *
     * @return bool
     */
    public function skip(): bool
    {
        $builder = $this->git->getProcessBuilder()
            ->add('rebase')
            ->add('--skip');

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **onto**          (_string_)  Starting point at which to create the new commits
     * - **no-verify**     (_boolean_) Bypasses the pre-rebase hook
     * - **force-rebase**  (_boolean_) Force the rebase even if the current branch is a descendant of the commit you are rebasing onto
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults(array(
            'onto'         => null,
            'no-verify'    => false,
            'force-rebase' => false
        ));

        $resolver->setAllowedTypes(array(
            'onto' => array('null', 'string')
        ));
    }

}