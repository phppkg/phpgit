<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

namespace PhpGit\Command;

use DateTime;
use Exception;
use PhpGit\Concern\AbstractCommand;
use Traversable;
use function strpos;

/**
 * Summarize 'git log' output - `git shortlog`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Shortlog extends AbstractCommand
{
    /**
     * Summarize 'git log' output
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $shortlog = $git->shortlog();
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     'John Doe <john@example.com>' => [
     *         0 => ['commit' => '589de67', 'date' => new \DateTime('2014-02-10 12:56:15 +0300'), 'subject' => 'Update README'],
     *         1 => ['commit' => '589de67', 'date' => new \DateTime('2014-02-15 12:56:15 +0300'), 'subject' => 'Update README'],
     *     ],
     *     //...
     * ]
     * ```
     *
     * @param string|array|Traversable $commits [optional] Defaults to HEAD
     *
     * @return array
     * @throws Exception
     */
    public function __invoke($commits = 'HEAD')
    {
        $builder = $this->getCommandBuilder()
            ->add('--numbered')
            ->add('--format=%h|%ci|%s')
            ->add('-w256,2,2')
            ->add('-e');

        if (!is_array($commits) && !($commits instanceof Traversable)) {
            $commits = [$commits];
        }

        foreach ($commits as $commit) {
            $builder->add($commit);
        }

        // $process = $builder->getProcess();
        // $process->setCommandLine(str_replace('--format=', '--format=%h|%ci|%s', $process->getCommandLine()));

        $output = $this->run($builder);
        $lines  = $this->split($output);
        $result = [];
        $author = null;

        foreach ($lines as $line) {
            if (strpos($line, ' ') !== 0) {
                if (preg_match('/([^<>]*? <[^<>]+>)/', $line, $matches)) {
                    $author          = $matches[1];
                    $result[$author] = [];
                }
                continue;
            }

            [$commit, $date, $subject] = explode('|', trim($line), 3);
            $result[$author][] = [
                'commit'  => $commit,
                'date'    => new DateTime($date),
                'subject' => $subject
            ];
        }

        return $result;
    }

    /**
     * Suppress commit description and provide a commit count summary only
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $shortlog = $git->shortlog->summary();
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     'John Doe <john@example.com>' => 153,
     *     //...
     * ]
     * ```
     *
     * @param string|array $commits [optional] Defaults to HEAD
     *
     * @return array
     */
    public function summary($commits = 'HEAD'): array
    {
        $builder = $this->getCommandBuilder()
            ->add('--numbered')
            ->add('--summary')
            ->add('-e');

        if (!is_array($commits) && !($commits instanceof Traversable)) {
            $commits = [$commits];
        }

        foreach ($commits as $commit) {
            $builder->add($commit);
        }

        $output = $this->run($builder);
        $lines  = $this->split($output);
        $result = [];

        foreach ($lines as $line) {
            [$commits, $author] = explode("\t", trim($line), 2);
            $result[$author] = (int)$commits;
        }

        return $result;
    }
}
