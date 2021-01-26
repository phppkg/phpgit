<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;
use Traversable;

/**
 * Add file contents to the index - `git add`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Add extends AbstractCommand
{

    /**
     * Add file contents to the index
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->add('file.txt');
     * $git->add('file.txt', ['force' => false, 'ignore-errors' => false);
     * ```
     *
     * ##### Options
     *
     * - **force**          (_boolean_) Allow adding otherwise ignored files
     * - **ignore-errors**  (_boolean_) Do not abort the operation
     *
     * @param string|array|Traversable $file    Files to add content from
     * @param array                    $options [optional] An array of options {@see Add::setDefaultOptions}
     *
     * @return bool
     *@throws GitException
     */
    public function __invoke($file, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('add');

        $this->addFlags($builder, $options);

        if (!is_array($file) && !($file instanceof Traversable)) {
            $file = array($file);
        }

        foreach ($file as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **force**          (_boolean_) Allow adding otherwise ignored files
     * - **ignore-errors**  (_boolean_) Do not abort the operation
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults(array(
            //'dry-run'        => false,
            'force'          => false,
            'ignore-errors'  => false,
            //'ignore-missing' => false,
        ));
    }

}