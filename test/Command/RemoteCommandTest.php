<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

use PhpGit\Git;

require_once __DIR__ . '/../BaseTestCase.php';

class RemoteCommandTest extends BaseTestCase
{
    public function testRemote(): void
    {
        $git = new Git();
        $git->clone('https://github.com/kzykhys/Text.git', $this->directory);
        $git->setRepository($this->directory);

        $remotes = $git->remote();

        $this->assertEquals([
            'origin' => [
                'fetch' => 'https://github.com/kzykhys/Text.git',
                'push'  => 'https://github.com/kzykhys/Text.git'
            ]
        ], $remotes);
    }

    public function testRemoteAdd(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');

        $remotes = $git->remote();

        $this->assertEquals([
            'origin' => [
                'fetch' => 'https://github.com/kzykhys/Text.git',
                'push'  => 'https://github.com/kzykhys/Text.git'
            ]
        ], $remotes);
    }

    public function testRemoteRename(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->remote->rename('origin', 'upstream');

        $remotes = $git->remote();
        $this->assertEquals([
            'upstream' => [
                'fetch' => 'https://github.com/kzykhys/Text.git',
                'push'  => 'https://github.com/kzykhys/Text.git'
            ]
        ], $remotes);
    }

    public function testRemoteRm(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->remote->rm('origin');

        $remotes = $git->remote();
        $this->assertEquals([], $remotes);
    }

    public function testRemoteShow(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');

        $this->assertNotEmpty($git->remote->show('origin'));
    }

    public function testRemotePrune(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->remote->prune('origin');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBadMethodCall(): void
    {
        $git = new Git();
        $git->remote->foo();
    }
}
