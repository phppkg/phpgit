<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGitTest\Command;

use PhpGit\Exception\GitException;
use PhpGit\Git;
use PhpGitTest\BasePhpGitTestCase;
use Symfony\Component\Filesystem\Filesystem;

class MergeTest extends BasePhpGitTestCase
{
    public function testMerge(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', 'foo');
        $git->add('test.txt');
        $git->commit('master');

        $git->checkout->create('develop');
        $filesystem->dumpFile($this->directory . '/test.txt', 'bar');
        $git->add('test.txt');
        $git->commit('develop');

        $git->checkout('master');

        $this->assertEquals('foo', file_get_contents($this->directory . '/test.txt'));

        $git->merge('develop');

        $this->assertEquals('bar', file_get_contents($this->directory . '/test.txt'));
    }

    public function testMergeFail(): void
    {
        $filesystem = new Filesystem();

        $this->expectException(GitException::class);
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        // branch:master
        $filesystem->dumpFile($this->directory . '/test.txt', 'foo');
        $git->add('test.txt');
        $git->commit('master');

        // branch:develop
        $git->checkout->create('develop');
        $filesystem->dumpFile($this->directory . '/test.txt', 'bar');
        $git->add('test.txt');
        $git->commit('develop');

        // branch:master
        $git->checkout('master');
        $filesystem->dumpFile($this->directory . '/test.txt', 'baz');
        $git->merge('develop');
    }

    public function testMergeAbort(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        // branch:master
        $filesystem->dumpFile($this->directory . '/test.txt', 'foo');
        $git->add('test.txt');
        $git->commit('master');

        // branch:develop
        $git->checkout->create('develop');
        $filesystem->dumpFile($this->directory . '/test.txt', 'bar');
        $git->add('test.txt');
        $git->commit('develop');

        // branch:master
        $git->checkout('master');
        $filesystem->dumpFile($this->directory . '/test.txt', 'baz');
        $git->add('test.txt');
        $git->commit('master');

        try {
            $git->merge('develop');
            $this->fail('$git->merge("develop") should fail');
        } catch (Exception $e) {
            $this->assertNotEmpty($e->getMessage());
        }

        $git->merge->abort();

        $this->assertEquals('baz', file_get_contents($this->directory . '/test.txt'));
    }
}
