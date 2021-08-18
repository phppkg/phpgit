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
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;

/**
 * Record changes to the repository - `git commit`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Commit extends AbstractCommand
{
    /**
     * Record changes to the repository
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/kzykhys/PhpGit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $git->add('README.md');
     * $git->commit('Fixes README.md');
     * ```
     *
     * ##### Options
     *
     * - **all**           (_boolean_) Stage files that have been modified and deleted
     * - **reuse-message** (_string_)  Take an existing commit object, and reuse the log message and the authorship information (including the timestamp) when creating the commit
     * - **squash**        (_string_)  Construct a commit message for use with rebase --autosquash
     * - **author**        (_string_)  Override the commit author
     * - **date**          (_string_)  Override the author date used in the commit
     * - **cleanup**       (_string_)  Can be one of verbatim, whitespace, strip, and default
     * - **amend**         (_boolean_) Used to amend the tip of the current branch
     *
     * @param string $message Use the given <$msg> as the commit message
     * @param array  $options [optional] An array of options {@see GitClone::setDefaultOptions}
     *
     * @return bool
     * @throws GitException
     */
    public function __invoke(string $message, array $options = []): bool
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();
        $builder->add('-m')->add($message);

        $this->addFlags($builder, $options, ['all', 'amend']);
        $this->addValues($builder, $options, ['reuse-message', 'squash', 'author', 'date', 'cleanup']);

        // $this->run($builder);
        $builder->run();

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **all**           (_boolean_) Stage files that have been modified and deleted
     * - **reuse-message** (_string_)  Take an existing commit object, and reuse the log message and the authorship information (including the timestamp) when creating the commit
     * - **squash**        (_string_)  Construct a commit message for use with rebase --autosquash
     * - **author**        (_string_)  Override the commit author
     * - **date**          (_string_)  Override the author date used in the commit
     * - **cleanup**       (_string_)  Can be one of verbatim, whitespace, strip, and default
     * - **amend**         (_boolean_) Used to amend the tip of the current branch
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'all'           => false,
            'reuse-message' => null,
            'squash'        => null,
            'author'        => null,
            'date'          => null,
            'cleanup'       => null,
            'amend'         => false
        ]);

        $resolver->setAllowedValues('cleanup', [
            null, 'default', 'verbatim', 'whitespace', 'strip'
        ]);
    }
}
