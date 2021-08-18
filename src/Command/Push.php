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
    public function __invoke($repository = null, $refspec = null, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options);

        if ($repository) {
            $builder->add($repository);

            if ($refspec) {
                $builder->add($refspec);
            }
        }

        $this->run($builder);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'all'    => false,
            'mirror' => false,
            'tags'   => false,
            'force'  => false
        ]);
    }
}
