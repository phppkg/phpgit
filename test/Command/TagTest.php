<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

use PhpGit\Exception\GitException;
use PhpGit\Git;
use Symfony\Component\Filesystem\Filesystem;



class TagTest extends BaseTestCase
{
    public function testTagDelete(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');
        $git->tag->create('v1.0.0');
        $git->tag->delete('v1.0.0');
        $this->assertCount(0, $git->tag());
    }

    /**
     * @expectedException GitException
     */
    public function testTagVerify(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');
        $git->tag->create('v1.0.0');
        $git->tag->verify('v1.0.0');
    }

    public function testCreateTagFromCommit(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');
        $log = $git->log(null, null, ['limit' => 1]);
        $git->tag->create('v1.0.0', $log[0]['hash']);
        $this->assertCount(1, $git->tag());
    }
}
