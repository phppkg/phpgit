<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use PhpGit\Exception\GitException;
use Symfony\Component\OptionsResolver\Options;
use Traversable;

/**
 * Join two or more development histories together - `git merge`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Merge extends AbstractCommand
{
    /**
     * Incorporates changes from the named commits into the current branch
     *
     * ```php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $git->merge('1.0');
     * $git->merge('1.1', 'Merge message', ['strategy' => 'ours']);
     * ```
     *
     * ##### Options
     *
     * - **no-ff**               (_boolean_) Do not generate a merge commit if the merge resolved as a fast-forward, only update the branch pointer
     * - **rerere-autoupdate**   (_boolean_) Allow the rerere mechanism to update the index with the result of auto-conflict resolution if possible
     * - **squash**              (_boolean_) Allows you to create a single commit on top of the current branch whose effect is the same as merging another branch
     * - **strategy**            (_string_)  Use the given merge strategy
     * - **strategy-option**     (_string_)  Pass merge strategy specific option through to the merge strategy
     *
     * @param string|array|Traversable $commit  Commits to merge into our branch
     * @param null                     $message [optional] Commit message to be used for the merge commit
     * @param array                    $options [optional] An array of options {@see Merge::setDefaultOptions}
     *
     * @return bool
     */
    public function __invoke($commit, $message = null, array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();

        $this->addFlags($builder, $options, ['no-ff', 'rerere-autoupdate', 'squash']);

        if (!is_array($commit) && !($commit instanceof Traversable)) {
            $commit = [$commit];
        }
        foreach ($commit as $value) {
            $builder->add($value);
        }

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * Abort the merge process and try to reconstruct the pre-merge state
     *
     * ```php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * try {
     *     $git->merge('dev');
     * } catch (PhpGit\Exception\GitException $e) {
     *     $git->merge->abort();
     * }
     * ```
     *
     * @return bool
     * @throws GitException
     */
    public function abort(): bool
    {
        $builder = $this->getCommandBuilder()->add('--abort');

        $this->run($builder->getProcess());

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * - **no-ff**               (_boolean_) Do not generate a merge commit if the merge resolved as a fast-forward, only update the branch pointer
     * - **rerere-autoupdate**   (_boolean_) Allow the rerere mechanism to update the index with the result of auto-conflict resolution if possible
     * - **squash**              (_boolean_) Allows you to create a single commit on top of the current branch whose effect is the same as merging another branch
     * - **strategy**            (_string_)  Use the given merge strategy
     * - **strategy-option**     (_string_)  Pass merge strategy specific option through to the merge strategy
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'no-ff'             => false,
            'rerere-autoupdate' => false,
            'squash'            => false,

            'strategy'        => null,
            'strategy-option' => null
        ]);
    }
}
