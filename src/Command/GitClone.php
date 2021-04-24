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
 * Clone a repository into a new directory - `git clone`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class GitClone extends AbstractCommand
{
    /**
     * @return string
     */
    public function getCommandName(): string
    {
        return 'clone';
    }

    /**
     * Clone a repository into a new directory
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/kzykhys/PhpGit.git', '/path/to/repo');
     * ```
     *
     * ##### Options
     *
     * - **shared** (_boolean_) Starts out without any object of its own
     * - **bare**   (_boolean_) Make a bare GIT repository
     *
     * @param string $repository The repository to clone from
     * @param null   $path       [optional] The name of a new directory to clone into
     * @param array  $options    [optional] An array of options {@see GitClone::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($repository, $path = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder()->add('--quiet');

        $this->addFlags($builder, $options);

        $builder->add($repository);

        if ($path) {
            $builder->add($path);
        }

        $this->run($builder);

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **shared** (_boolean_) Starts out without any object of its own
     * - **bare**   (_boolean_) Make a bare GIT repository
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'shared' => false,
            'bare'   => false
        ]);
    }
}
