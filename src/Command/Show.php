<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;

/**
 * Show various types of objects - `git show`
 *
 * @author Kazuyuki Hayashi
 */
class Show extends AbstractCommand
{
    /**
     * Shows one or more objects (blobs, trees, tags and commits)
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * echo $git->show('3ddee587e209661c8265d5bfd0df999836f6dfa2');
     * ```
     *
     * ##### Options
     *
     * - **format**        (_string_)  Pretty-print the contents of the commit logs in a given format, where <format> can be one of oneline, short, medium, full, fuller, email, raw and format:<string>
     * - **abbrev-commit** (_boolean_) Instead of showing the full 40-byte hexadecimal commit object name, show only a partial prefix
     *
     * @param string $object  The names of objects to show
     * @param array  $options [optional] An array of options {@see Show::setDefaultOptions}
     *
     * @return string
     */
    public function __invoke($object, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->git->getCommandBuilder()
            ->add('show');

        $this->addFlags($builder, $options, ['abbrev-commit']);

        if ($options['format']) {
            $builder->add('--format=' . $options['format']);
        }

        $builder->add($object);

        return $this->run($builder->getProcess());
    }

    /**
     * {@inheritdoc}
     *
     * - **format**        (_string_)  Pretty-print the contents of the commit logs in a given format, where <format> can be one of oneline, short, medium, full, fuller, email, raw and format:<string>
     * - **abbrev-commit** (_boolean_) Instead of showing the full 40-byte hexadecimal commit object name, show only a partial prefix
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'format'        => null,
            'abbrev-commit' => false
        ]);

        $resolver->setAllowedTypes([
            'format' => ['null', 'string'],
        ]);
    }
}
