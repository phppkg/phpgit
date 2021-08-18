<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\Concern\AbstractCommand;
use Symfony\Component\OptionsResolver\Options;
use Traversable;

/**
 * Remove files from the working tree and from the index - `git rm`
 *
 * @author Kazuyuki Hayashi
 */
class Rm extends AbstractCommand
{
    /**
     * Remove files from the working tree and from the index
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->rm('CHANGELOG-1.0-1.1.txt', ['force' => true]);
     * ```
     *
     * ##### Options
     *
     * - **force**     (_boolean_) Override the up-to-date check
     * - **cached**    (_boolean_) Unstage and remove paths only from the index
     * - **recursive** (_boolean_) Allow recursive removal when a leading directory name is given
     *
     * @param string|array|Traversable $file    Files to remove. Fileglobs (e.g.  *.c) can be given to remove all matching files.
     * @param array                    $options [optional] An array of options {@see Rm::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($file, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['force', 'cached']);

        if ($options['recursive']) {
            $builder->add('-r');
        }

        if (!is_array($file) && !($file instanceof Traversable)) {
            $file = [$file];
        }

        foreach ($file as $value) {
            $builder->add($value);
        }

        $this->run($builder);

        return true;
    }

    /**
     * Equivalent to $git->rm($file, ['cached' => true]);
     *
     * ##### Options
     *
     * - **force**     (_boolean_) Override the up-to-date check
     * - **recursive** (_boolean_) Allow recursive removal when a leading directory name is given
     *
     * @param string|array|Traversable $file    Files to remove. Fileglobs (e.g.  *.c) can be given to remove all matching files.
     * @param array                    $options [optional] An array of options {@see Rm::setDefaultOptions}
     *
     * @return bool
     */
    public function cached($file, array $options = []): bool
    {
        $options['cached'] = true;

        return $this->__invoke($file, $options);
    }

    /**
     * {@inheritdoc}
     *
     * - **force**     (_boolean_) Override the up-to-date check
     * - **cached**    (_boolean_) Unstage and remove paths only from the index
     * - **recursive** (_boolean_) Allow recursive removal when a leading directory name is given
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'force'     => false,
            'cached'    => false,
            'recursive' => false
        ]);
    }
}
