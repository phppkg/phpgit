<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

use PhpGit\Exception\GitException;
use PhpGit\Git;
use PHPUnit\Framework\TestCase;

class GitTest extends TestCase
{
    public function testGetVersion(): void
    {
        $git = new Git();
        $this->assertNotEmpty($git->getVersion());
    }

    /**
     * @expectedException GitException
     */
    public function testInvalidGitBinary(): void
    {
        $git = new Git();
        $git->setBin('/foo/bar');
        $git->getVersion();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testBadMethodCall(): void
    {
        $git = new Git();
        $git->foo();
    }
}
