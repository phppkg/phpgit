<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;
use Traversable;

/**
 * Create an archive of files from a named tree - `git archive`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Archive extends AbstractCommand
{

    /**
     * Create an archive of files from a named tree
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->archive('repo.zip', 'master', null, ['format' => 'zip']);
     * ```
     *
     * ##### Options
     *
     * - **format** (_boolean_) Format of the resulting archive: tar or zip
     * - **prefix** (_boolean_) Prepend prefix/ to each filename in the archive
     *
     * @param string $file    The filename
     * @param null   $tree    [optional] The tree or commit to produce an archive for
     * @param null   $path    [optional] If one or more paths are specified, only these are included
     * @param array  $options [optional] An array of options {@see Archive::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($file, $tree = null, $path = null, array $options = array())
    {
        $options = $this->resolve($options);
        $builder = $this->git->getProcessBuilder()
            ->add('archive');

        if ($options['format']) {
            $builder->add('--format=' . $options['format']);
        }

        if ($options['prefix']) {
            $builder->add('--prefix=' . $options['prefix']);
        }

        $builder->add('-o')->add($file);

        if ($tree) {
            $builder->add($tree);
        }

        if (!is_array($path) && !($path instanceof Traversable)) {
            $path = array($path);
        }

        foreach ($path as $value) {
            $builder->add($value);
        }

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **format** (_boolean_) Format of the resulting archive: tar or zip
     * - **prefix** (_boolean_) Prepend prefix/ to each filename in the archive
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'format' => null,
            'prefix' => null
        ]);

        $resolver->setAllowedTypes([
            'format' => ['null', 'string'],
            'prefix' => ['null', 'string']
        ]);

        $resolver->setAllowedValues([
            'format' => ['tar', 'zip']
        ]);
    }


}