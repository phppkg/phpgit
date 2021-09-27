<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

use PhpGit\Git;
use Symfony\Component\Filesystem\Filesystem;

class ResetTest extends BaseTestCase
{
    public function testReset(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('README.md');

        $git->reset('README.md', 'HEAD');
    }

    public function testResetSoft(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');

        $git->reset->soft();
    }

    public function testResetMixed(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');

        $git->reset->mixed();
    }

    public function testResetHard(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');

        $git->reset->hard('HEAD');
    }

    public function testResetMerge(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');

        $git->reset->merge();
    }

    public function testResetKeep(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');

        $git->reset->keep();
    }

    public function testResetInvalidMode(): void
    {
        $filesystem = new Filesystem();

        $this->expectException(InvalidArgumentException::class);
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');

        $git->reset->mode('foo');
    }
}
