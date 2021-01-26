<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;

/**
 * Create an empty git repository or reinitialize an existing one - `git init`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Init extends AbstractCommand
{

    /**
     * Create an empty git repository or reinitialize an existing one
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->init('/path/to/repo1');
     * $git->init('/path/to/repo2', array('shared' => true, 'bare' => true));
     * ```
     *
     * ##### Options
     *
     * - **shared** (_boolean_) Specify that the git repository is to be shared amongst several users
     * - **bare**   (_boolean_) Create a bare repository
     *
     * @param string $path    The directory to create an empty repository
     * @param array  $options [optional] An array of options {@see Init::setDefaultOptions}
     *
     * @return bool
     *@throws GitException
     */
    public function __invoke($path, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('init');

        $this->addFlags($builder, $options, array('shared', 'bare'));

        $process = $builder->add($path)->getProcess();
        $this->git->run($process);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **shared** (_boolean_) Specify that the git repository is to be shared amongst several users
     * - **bare**   (_boolean_) Create a bare repository
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults(array(
            'shared' => false,
            'bare'   => false
        ));
    }

}