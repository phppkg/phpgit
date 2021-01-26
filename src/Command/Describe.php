<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;

/**
 * Show the most recent tag that is reachable from a commit - `git describe`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Describe extends AbstractCommand
{

    /**
     * Returns the most recent tag that is reachable from a commit
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->tag->create('v1.0.0');
     * $git->commit('Fixes #14');
     * echo $git->describe('HEAD', ['tags' => true]);
     * ```
     *
     * ##### Output Example
     *
     * ```
     * v1.0.0-1-g7049efc
     * ```
     *
     * ##### Options
     *
     * - **all**    (_boolean_) Enables matching any known branch, remote-tracking branch, or lightweight tag
     * - **tags**   (_boolean_) Enables matching a lightweight (non-annotated) tag
     * - **always** (_boolean_) Show uniquely abbreviated commit object as fallback
     *
     * @param null  $committish [optional] Committish object names to describe.
     * @param array $options    [optional] An array of options {@see Describe::setDefaultOptions}
     *
     * @return string
     */
    public function __invoke($committish = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('describe');

        $this->addFlags($builder, $options, array());

        if ($committish) {
            $builder->add($committish);
        }

        return trim($this->git->run($builder->getProcess()));
    }

    /**
     * Equivalent to $git->describe($committish, ['tags' => true]);
     *
     * @param null  $committish [optional] Committish object names to describe.
     * @param array $options    [optional] An array of options {@see Describe::setDefaultOptions}
     *
     * @return string
     */
    public function tags($committish = null, array $options = array()): string
    {
        $options['tags'] = true;

        return $this->__invoke($committish, $options);
    }

    /**
     * {@inheritdoc}
     *
     * - **all**    (_boolean_) Enables matching any known branch, remote-tracking branch, or lightweight tag
     * - **tags**   (_boolean_) Enables matching a lightweight (non-annotated) tag
     * - **always** (_boolean_) Show uniquely abbreviated commit object as fallback
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults(array(
            'all'    => false,
            'tags'   => false,
            'always' => false,
        ));
    }

}
