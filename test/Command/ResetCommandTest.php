<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

use PhpGit\Git;
use Symfony\Component\Filesystem\Filesystem;

require_once __DIR__ . '/../BaseTestCase.php';

class ResetCommandTest extends BaseTestCase
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

    /**
     * @expectedException InvalidArgumentException
     */
    public function testResetInvalidMode(): void
    {
        $filesystem = new Filesystem();

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
