<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use Iterator;
use PhpGit\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;
use Traversable;

/**
 * Move or rename a file, a directory, or a symlink - `git mv`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Mv extends AbstractCommand
{
    /**
     * Move or rename a file, a directory, or a symlink
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->mv('UPGRADE-1.0.md', 'UPGRADE-1.1.md');
     * ```
     *
     * ##### Options
     *
     * - **force** (_boolean_) Force renaming or moving of a file even if the target exists
     *
     * @param string|array|Iterator $source      The files to move
     * @param string                $destination The destination
     * @param array                 $options     [optional] An array of options {@see Mv::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($source, $destination, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getCommandBuilder()
            ->add('mv');

        $this->addFlags($builder, $options, ['force']);

        if (!is_array($source) && !($source instanceof Traversable)) {
            $source = [$source];
        }

        foreach ($source as $value) {
            $builder->add($value);
        }

        $builder->add($destination);

        $this->git->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **force** (_boolean_) Force renaming or moving of a file even if the target exists
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'force' => false
        ]);
    }
}
