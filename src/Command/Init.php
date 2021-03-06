<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\Concern\AbstractCommand;
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
     * @throws GitException
     */
    public function __invoke(string $path, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['shared', 'bare']);

        $builder->add($path);

        $this->run($builder);

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
        $resolver->setDefaults([
            'shared' => false,
            'bare'   => false
        ]);
    }
}
