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


class ShortlogTest extends BasePhpGitTestCase
{
    public function testShortlog(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->config->set('user.name', 'Name One');
        $git->config->set('user.email', 'one@example.com');
        $filesystem->dumpFile($this->directory . '/test.txt', '');
        $git->add('test.txt');
        $git->commit('1');
        $filesystem->dumpFile($this->directory . '/test2.txt', '');
        $git->add('test2.txt');
        $git->commit('2');

        $git->config->set('user.name', 'Name Two');
        $git->config->set('user.email', 'two@example.com');
        $filesystem->dumpFile($this->directory . '/test3.txt', '');
        $git->add('test3.txt');
        $git->commit('3');

        $shortlog = $git->shortlog();

        $this->assertCount(2, $shortlog);
        $this->assertCount(2, $shortlog['Name One <one@example.com>']);
        $this->assertCount(1, $shortlog['Name Two <two@example.com>']);
        $this->assertEquals('1', $shortlog['Name One <one@example.com>'][0]['subject']);
    }

    public function testShortlogSummary(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->config->set('user.name', 'Name One');
        $git->config->set('user.email', 'one@example.com');
        $filesystem->dumpFile($this->directory . '/test.txt', '');
        $git->add('test.txt');
        $git->commit('1');
        $filesystem->dumpFile($this->directory . '/test2.txt', '');
        $git->add('test2.txt');
        $git->commit('2');

        $git->config->set('user.name', 'Name Two');
        $git->config->set('user.email', 'two@example.com');
        $filesystem->dumpFile($this->directory . '/test3.txt', '');
        $git->add('test3.txt');
        $git->commit('3');

        $summary = $git->shortlog->summary();

        $this->assertEquals([
            'Name One <one@example.com>' => 2,
            'Name Two <two@example.com>' => 1
        ], $summary);
    }
}
