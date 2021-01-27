<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

use PhpGit\Exception\GitException;
use PhpGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__ . '/../BaseTestCase.php';

class RebaseTest extends BaseTestCase
{
    public function testRebase(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', '123');
        $git->add('test.txt');
        $git->commit('initial commit');

        $git->checkout->create('next');
        $filesystem->dumpFile($this->directory . '/test2.txt', '123');
        $git->add('test2.txt');
        $git->commit('test');

        $git->checkout('master');
        $git->rebase('next', 'master');

        $this->assertFileExists($this->directory . '/test2.txt');
    }

    public function testRebaseOnto(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/test.txt', '123');
        $git->add('test.txt');
        $git->commit('initial commit');

        $git->checkout->create('next');
        $filesystem->dumpFile($this->directory . '/test2.txt', '123');
        $git->add('test2.txt');
        $git->commit('test');

        $git->checkout->create('topic', 'next');
        $filesystem->dumpFile($this->directory . '/test3.txt', '123');
        $git->add('test3.txt');
        $git->commit('test');

        $git->rebase('next', null, ['onto' => 'master']);
        $this->assertFileNotExists($this->directory . '/test2.txt');
    }

    public function testRebaseContinue(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', 'foo');
        $git->add('test.txt');
        $git->commit('initial commit');

        $git->checkout->create('next');
        $filesystem->dumpFile($this->directory . '/test.txt', 'bar');
        $git->add('test.txt');
        $git->commit('next commit');

        $git->checkout('master');
        $filesystem->dumpFile($this->directory . '/test.txt', 'baz');
        $git->add('test.txt');
        $git->commit('master commit');

        try {
            $git->rebase('next');
            $this->fail('GitException should be thrown');
        } catch (GitException $e) {
        }

        $filesystem->dumpFile($this->directory . '/test.txt', 'foobar');
        $git->add('test.txt');
        $git->rebase->continues();
    }

    public function testRebaseAbort(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', 'foo');
        $git->add('test.txt');
        $git->commit('initial commit');

        $git->checkout->create('next');
        $filesystem->dumpFile($this->directory . '/test.txt', 'bar');
        $git->add('test.txt');
        $git->commit('next commit');

        $git->checkout('master');
        $filesystem->dumpFile($this->directory . '/test.txt', 'baz');
        $git->add('test.txt');
        $git->commit('master commit');

        try {
            $git->rebase('next');
            $this->fail('GitException should be thrown');
        } catch (GitException $e) {
        }

        $git->rebase->abort();
    }

    public function testRebaseSkip(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', 'foo');
        $git->add('test.txt');
        $git->commit('initial commit');

        $git->checkout->create('next');
        $filesystem->dumpFile($this->directory . '/test.txt', 'bar');
        $git->add('test.txt');
        $git->commit('next commit');

        $git->checkout('master');
        $filesystem->dumpFile($this->directory . '/test.txt', 'baz');
        $git->add('test.txt');
        $git->commit('master commit');

        try {
            $git->rebase('next');
            $this->fail('GitException should be thrown');
        } catch (GitException $e) {
        }

        $git->rebase->skip();
    }
}
