<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

use PhpGit\Git;



class RemoteTest extends BaseTestCase
{
    public function testRemote(): void
    {
        $git = new Git();
        $git->clone('https://github.com/phppkg/phpgit.git', $this->directory);
        $git->setRepository($this->directory);

        $remotes = $git->remote();

        $this->assertEquals([
            'origin' => [
                'fetch' => 'https://github.com/phppkg/phpgit.git',
                'push'  => 'https://github.com/phppkg/phpgit.git'
            ]
        ], $remotes);
    }

    public function testRemoteAdd(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');

        $remotes = $git->remote();

        $this->assertEquals([
            'origin' => [
                'fetch' => 'https://github.com/phppkg/phpgit.git',
                'push'  => 'https://github.com/phppkg/phpgit.git'
            ]
        ], $remotes);
    }

    public function testRemoteRename(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
        $git->remote->rename('origin', 'upstream');

        $remotes = $git->remote();
        $this->assertEquals([
            'upstream' => [
                'fetch' => 'https://github.com/phppkg/phpgit.git',
                'push'  => 'https://github.com/phppkg/phpgit.git'
            ]
        ], $remotes);
    }

    public function testRemoteRm(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
        $git->remote->rm('origin');

        $remotes = $git->remote();
        $this->assertEquals([], $remotes);
    }

    public function testRemoteShow(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');

        $this->assertNotEmpty($git->remote->show('origin'));
    }

    public function testRemotePrune(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
        $git->remote->prune('origin');
    }

    public function testBadMethodCall(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $git = new Git();
        $git->remote->foo();
    }
}
