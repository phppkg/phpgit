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

/**
 * List the contents of a tree object - `git ls-tree`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Tree extends AbstractCommand
{
    /**
     * Returns the contents of a tree object
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->clone('https://github.com/kzykhys/PhpGit.git', '/path/to/repo');
     * $git->setRepository('/path/to/repo');
     * $tree = $git->tree('master');
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     ['mode' => '100644', 'type' => 'blob', 'hash' => '1f100ce9855b66111d34b9807e47a73a9e7359f3', 'file' => '.gitignore', 'sort' => '2:.gitignore'],
     *     ['mode' => '100644', 'type' => 'blob', 'hash' => 'e0bfe494537037451b09c32636c8c2c9795c05c0', 'file' => '.travis.yml', 'sort' => '2:.travis.yml'],
     *     ['mode' => '040000', 'type' => 'tree', 'hash' => '8d5438e79f77cd72de80c49a413f4edde1f3e291', 'file' => 'bin', 'sort' => '1:.bin'],
     * ]
     * ```
     *
     * @param string $branch The commit
     * @param string $path   The path
     *
     * @return array
     */
    public function __invoke(string $branch = 'master', string $path = '')
    {
        $objects = [];
        $builder = $this->getCommandBuilder();
        $builder->add('ls-tree')->add($branch . ':' . $path);

        $output = $this->run($builder);

        $lines = $this->split($output);
        $types = [
            'submodule' => 0,
            'tree'      => 1,
            'blob'      => 2
        ];

        foreach ($lines as $line) {
            [$meta, $file] = explode("\t", $line);
            [$mode, $type, $hash] = explode(' ', $meta);

            $objects[] = [
                'sort' => sprintf('%d:%s', $types[$type], $file),
                'mode' => $mode,
                'type' => $type,
                'hash' => $hash,
                'file' => $file
            ];
        }

        return $objects;
    }
}
