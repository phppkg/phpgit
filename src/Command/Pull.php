<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;

/**
 * Fetch from and merge with another repository or a local branch - `git pull`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Pull extends AbstractCommand
{

    /**
     * Fetch from and merge with another repository or a local branch
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->pull('origin', 'master');
     * ```
     *
     * @param null  $repository   The "remote" repository that is the source of a fetch or pull operation
     * @param null  $refspec      The format of a <refspec> parameter is an optional plus +,
     *                            followed by the source ref <src>, followed by a colon :, followed by the destination ref <dst>
     * @param array $options      [optional] An array of options {@see Pull::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($repository = null, $refspec = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('pull');

        if ($repository) {
            $builder->add($repository);

            if ($refspec) {
                $builder->add($refspec);
            }
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(Options $resolver): void
    {

    }

}