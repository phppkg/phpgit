<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGitTest;

use PhpGit\Exception\GitException;
use PhpGit\Git;

class GitTest extends BasePhpGitTestCase
{
    public function testGetVersion(): void
    {
        $git = new Git();
        $this->assertNotEmpty($git->getVersion());
    }

    public function testInvalidGitBinary(): void
    {
        $this->expectException(GitException::class);
        $git = new Git();
        $git->setBin('/foo/bar');
        $git->getVersion();
    }

    public function testBadMethodCall(): void
    {
        $this->expectException(GitException::class);
        $git = new Git();
        $git->foo();
    }
}
