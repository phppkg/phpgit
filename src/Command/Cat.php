<?php

namespace PhpGit\Command;

use PhpGit\AbstractCommand;
use PhpGit\Exception\GitException;

/**
 * Provide content or type and size information for repository objects - `git cat-file`
 *
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class Cat extends AbstractCommand
{

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
     * @throws GitException
     * @return string
     */
    public function blob($object): string
    {
        $process = $this->git->getProcessBuilder()
            ->add('cat-file')
            ->add('blob')
            ->add($object)
            ->getProcess();

        return $this->git->run($process);
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
     * @throws GitException
     * @return string
     */
    public function type($object): string
    {
        $process = $this->git->getProcessBuilder()
            ->add('cat-file')
            ->add('-t')
            ->add($object)
            ->getProcess();

        return trim($this->git->run($process));
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
     * @throws GitException
     * @return string
     */
    public function size($object): string
    {
        $process = $this->git->getProcessBuilder()
            ->add('cat-file')
            ->add('-s')
            ->add($object)
            ->getProcess();

        return trim($this->git->run($process));
    }

}