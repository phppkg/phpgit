<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

use PhpGit\Git;
use Symfony\Component\Filesystem\Filesystem;



class PushTest extends BaseTestCase
{
    public function testPush(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory, ['shared' => true, 'bare' => true]);

        $git->clone('file://' . realpath($this->directory), $this->directory . '2');
        $git->setRepository($this->directory . '2');

        $filesystem->dumpFile($this->directory . '2/test.txt', 'foobar');
        $git->add('test.txt');
        $git->commit('test');
        $git->push('origin', 'master');

        $git->clone('file://' . realpath($this->directory), $this->directory . '3');

        $this->assertFileExists($this->directory . '3/test.txt');

        $filesystem->remove($this->directory . '2');
        $filesystem->remove($this->directory . '3');
    }
}
