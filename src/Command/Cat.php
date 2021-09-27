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
    public function blob(string $object): string
    {
        $builder = $this->getCommandBuilder()
            ->add('blob')
            ->add($object);

        return $this->run($builder);
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
    public function type(string $object): string
    {
        $builder = $this->getCommandBuilder()
            ->add('-t')
            ->add($object);

        return trim($this->run($builder));
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
    public function size(string $object): string
    {
        $builder = $this->getCommandBuilder()
            ->add('-s')
            ->add($object);

        return trim($this->run($builder));
    }
}
