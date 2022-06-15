<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGitTest\Command;

use PhpGit\Git;
use PhpGitTest\BasePhpGitTestCase;
use Symfony\Component\Filesystem\Filesystem;


class StashTest extends BasePhpGitTestCase
{
    public function testStash(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash();

        $this->assertEquals('hello', file_get_contents($this->directory . '/README.md'));
    }

    public function testStashSave(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash->save('stash test');

        $this->assertEquals('hello', file_get_contents($this->directory . '/README.md'));
    }

    public function testStashList(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash();

        $stashes = $git->stash->lists();

        $this->assertCount(1, $stashes);
        $this->assertEquals('master', $stashes[0]['branch']);
        $this->assertStringEndsWith('Initial commit', $stashes[0]['message']);
    }

    public function testStashShow(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash();
        $git->stash->show('stash@{0}');
    }

    public function testStashDrop(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash();
        $git->stash->drop();

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash();
        $git->stash->drop('stash@{0}');

        $this->assertCount(0, $git->stash->lists());
    }

    public function testStashPop(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash->save('stash#1');

        $filesystem->dumpFile($this->directory . '/README.md', 'bar');
        $git->stash->save('stash#2');
        $git->stash->pop('stash@{1}');

        $this->assertEquals('hi!', file_get_contents($this->directory . '/README.md'));
        $this->assertCount(1, $git->stash->lists());
    }

    public function testStashApply(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash->save('stash#1');

        $filesystem->dumpFile($this->directory . '/README.md', 'bar');
        $git->stash->save('stash#2');
        $git->stash->apply('stash@{1}');

        $this->assertEquals('hi!', file_get_contents($this->directory . '/README.md'));
        $this->assertCount(2, $git->stash->lists());
    }

    public function testStashBranch(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash();

        $git->stash->branch('dev', 'stash@{0}');
        $status = $git->status();

        $this->assertEquals('dev', $status['branch']);
        $this->assertEquals('hi!', file_get_contents($this->directory . '/README.md'));
    }

    public function testStashClear(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $git->stash();
        $git->stash->clear();

        $this->assertCount(0, $git->stash->lists());
    }

    public function testStashCreate(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hi!');
        $object = $git->stash->create();

        $this->assertNotEmpty($object);
    }
}
