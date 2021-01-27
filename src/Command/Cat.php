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
use PhpGit\Exception\GitException;
use function trim;

/**
 * Provide content or type and size information for repository objects - `git cat-file`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Cat extends AbstractCommand
{
    public function getCommandName(): string
    {
        return 'cat-file';
    }

    /**
     * Returns the contents of blob object
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $contents = $git->cat->blob('e69de29bb2d1d6434b8b29ae775ad8');
     * ```
     *
     * @param string $object The name of the blob object to show
     *
     * @return string
     * @throws GitException
     */
    public function blob($object): string
    {
        $process = $this->getCommandBuilder()
            ->add('blob')
            ->add($object)
            ->getProcess();

        return $this->run($process);
    }

    /**
     * Returns the object type identified by **$object**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $type = $git->cat->type('e69de29bb2d1d6434b8b29ae775ad8');
     * ```
     *
     * @param string $object The name of the object to show
     *
     * @return string
     * @throws GitException
     */
    public function type($object): string
    {
        $process = $this->getCommandBuilder()
            ->add('-t')
            ->add($object)
            ->getProcess();

        return trim($this->run($process));
    }

    /**
     * Returns the object size identified by **$object**
     *
     * ``` php
     * $git = new PhpGit\Git();
     * $git->setRepository('/path/to/repo');
     * $type = $git->cat->size('e69de29bb2d1d6434b8b29ae775ad8');
     * ```
     *
     * @param string $object The name of the object to show
     *
     * @return string
     * @throws GitException
     */
    public function size($object): string
    {
        $process = $this->getCommandBuilder()
            ->add('-s')
            ->add($object)
            ->getProcess();

        return trim($this->run($process));
    }
}
