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

class DescribeTest extends BaseTestCase
{
    public function testDescribeTags(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('README.md');
        $git->commit('Initial commit');
        $git->tag->create('v1.0.0');
        $version = $git->describe->tags('HEAD');

        $this->assertEquals('v1.0.0', $version);

        $filesystem->dumpFile($this->directory . '/README.md', 'hello2');
        $git->add('README.md');
        $git->commit('Fixes README');
        $version = $git->describe->tags('HEAD');

        $this->assertStringStartsWith('v1.0.0', $version);
        $this->assertStringEndsNotWith('v1.0.0', $version);
    }
}
