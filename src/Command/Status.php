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
 * Show the working tree status - `git status`
 *
 *   = unmodified
 * M = modified
 * A = added
 * D = deleted
 * R = renamed
 * C = copied
 * U = updated but unmerged
 *
 * X          Y     Meaning
 * -------------------------------------------------
 * [MD]   not updated
 * M        [ MD]   updated in index
 * A        [ MD]   added to index
 * D         [ M]   deleted from index
 * R        [ MD]   renamed in index
 * C        [ MD]   copied in index
 * [MARC]           index and work tree matches
 * [ MARC]     M    work tree changed since index
 * [ MARC]     D    deleted in work tree
 * -------------------------------------------------
 * D           D    unmerged, both deleted
 * A           U    unmerged, added by us
 * U           D    unmerged, deleted by them
 * U           A    unmerged, added by them
 * D           U    unmerged, deleted by us
 * A           A    unmerged, both added
 * U           U    unmerged, both modified
 * -------------------------------------------------
 * ?           ?    untracked
 * !           !    ignored
 * -------------------------------------------------
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Status extends AbstractCommand
{
    public const UNMODIFIED           = ' ';

    public const MODIFIED             = 'M';

    public const ADDED                = 'A';

    public const DELETED              = 'D';

    public const RENAMED              = 'R';

    public const COPIED               = 'C';

    public const UPDATED_BUT_UNMERGED = 'U';

    public const UNTRACKED            = '?';

    public const IGNORED              = '!';

    /**
     * Returns the working tree status
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $status = $git->status();
     * ```
     *
     * ##### Constants
     *
     * - StatusCommand::UNMODIFIED            [=' '] unmodified
     * - StatusCommand::MODIFIED              [='M'] modified
     * - StatusCommand::ADDED                 [='A'] added
     * - StatusCommand::DELETED               [='D'] deleted
     * - StatusCommand::RENAMED               [='R'] renamed
     * - StatusCommand::COPIED                [='C'] copied
     * - StatusCommand::UPDATED_BUT_UNMERGED  [='U'] updated but unmerged
     * - StatusCommand::UNTRACKED             [='?'] untracked
     * - StatusCommand::IGNORED               [='!'] ignored
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     'branch' => 'master',
     *     'changes' => [
     *         ['file' => 'item1.txt', 'index' => 'A', 'work_tree' => 'M'],
     *         ['file' => 'item2.txt', 'index' => 'A', 'work_tree' => ' '],
     *         ['file' => 'item3.txt', 'index' => '?', 'work_tree' => '?'],
     *     ]
     * ]
     * ```
     *
     * ##### Options
     *
     * - **ignored** (_boolean_) Show ignored files as well
     *
     * @param array $options [optional] An array of options {@see Status::setDefaultOptions}
     *
     * @return mixed
     */
    public function __invoke(array $options = [])
    {
        $options = $this->resolve($options);
        $builder = $this->getCommandBuilder();
        $builder->add('--porcelain')->add('-s')->add('-b')->add('--null');

        $this->addFlags($builder, $options);

        // $process = $builder->getProcess();
        $result  = ['branch' => null, 'changes' => []];
        $output  = $this->run($builder);

        [$branch, $changes] = preg_split('/(\0|\n)/', $output, 2);
        $lines = $this->split($changes, true);

        if (substr($branch, -11) === '(no branch)') {
            $result['branch'] = null;
        } elseif (preg_match('/([^ ]*)\.\.\..*?\[.*?\]$/', $branch, $matches)) {
            $result['branch'] = $matches[1];
        } elseif (preg_match('/ ([^ ]*)$/', $branch, $matches)) {
            $result['branch'] = $matches[1];
        }

        foreach ($lines as $line) {
            $result['changes'][] = [
                'file'      => substr($line, 3),
                'index'     => substr($line, 0, 1),
                'work_tree' => substr($line, 1, 1)
            ];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     *
     * - **ignored** (_boolean_) Show ignored files as well
     */
    public function setDefaultOptions(Options $resolver): void
    {
        $resolver->setDefaults([
            'ignored' => false
        ]);
    }
}
