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


class TreeTest extends BasePhpGitTestCase
{
    public function testListBranch(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $filesystem->dumpFile($this->directory . '/README.md', 'hello');
        $git->add('.');
        $git->commit('Initial commit');

        $result = $git->tree('master');

        $this->assertIsArray($result);
        $this->assertCount(1, $result);

        $inner = array_shift($result);

        $this->assertIsArray($inner);
        $this->assertArrayHasKey('sort', $inner);
        $this->assertEquals('2:README.md', $inner['sort']);

        $this->assertArrayHasKey('type', $inner);
        $this->assertEquals('blob', $inner['type']);

        $this->assertArrayHasKey('mode', $inner);
        $this->assertEquals(100644, $inner['mode']);

        $this->assertArrayHasKey('hash', $inner);
        $this->assertEquals('b6fc4c620b67d95f953a5c1c1230aaab5db5a1b0', $inner['hash']);

        $this->assertArrayHasKey('file', $inner);
        $this->assertEquals('README.md', $inner['file']);
    }
}
