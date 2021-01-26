<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;

/**
 * Update remote refs along with associated objects - `git push`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Push extends AbstractCommand
{

    /**
     * Update remote refs along with associated objects
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->push('origin', 'master');
     * ```
     *
     * @param null  $repository The "remote" repository that is destination of a push operation
     * @param null  $refspec    Specify what destination ref to update with what source object
     * @param array $options    [optional] An array of options {@see Push::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($repository = null, $refspec = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('push');

        $this->addFlags($builder, $options);

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
        $resolver->setDefaults(array(
            'all'    => false,
            'mirror' => false,
            'tags'   => false,
            'force'  => false
        ));
    }

}