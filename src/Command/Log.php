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
 * Show commit logs - `git log`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Log extends AbstractCommand
{
    /**
     * Returns the commit logs
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $logs = $git->log(array('limit' => 10));
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     0 => [
     *         'hash'  => '1a821f3f8483747fd045eb1f5a31c3cc3063b02b',
     *         'name'  => 'John Doe',
     *         'email' => 'john@example.com',
     *         'date'  => 'Fri Jan 17 16:32:49 2014 +0900',
     *         'title' => 'Initial Commit'
     *     ],
     *     1 => [
     *         //...
     *     ]
     * ]
     * ```
     *
     * ##### Options
     *
     * - **limit** (_integer_) Limits the number of commits to show
     * - **skip**  (_integer_) Skip number commits before starting to show the commit output
     *
     * @param string $revRange [optional] Show only commits in the specified revision range
     * @param null   $path     [optional] Show only commits that are enough to explain how the files that match the specified paths came to be
     * @param array  $options  [optional] An array of options {@see Log::setDefaultOptions}
     *
     * @return array
     */
    public function __invoke($revRange = '', $path = null, array $options = [])
    {
        $commits = [];
        $options = $this->resolve($options);

        $builder = $this->getCommandBuilder()
            ->add('-n')->add($options['limit'])
            ->add('--skip=' . $options['skip'])
            ->add('--format=%H||%aN||%aE||%aD||%s');

        if ($revRange) {
            $builder->add($revRange);
        }

        if ($path) {
            $builder->add('--')->add($path);
        }

        $output = $this->run($builder->getProcess());
        $lines  = $this->split($output);

        foreach ($lines as $line) {
            [$hash, $name, $email, $date, $title] = preg_split('/\|\|/', $line, -1, PREG_SPLIT_NO_EMPTY);
            $commits[] = [
                'hash'  => $hash,
                'name'  => $name,
                'email' => $email,
                'date'  => $date,
                'title' => $title
            ];
        }

        return $commits;
    }

    /**
     * {@inheritdoc}
     *
     * - **limit** (_integer_) Limits the number of commits to show
     * - **skip**  (_integer_) Skip number commits before starting to show the commit output
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'limit' => 10,
            'skip'  => 0
        ]);
    }
}
