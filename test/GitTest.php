<?php

use PhpGit\Exception\GitException;
use PhpGit\Git;

class GitTest extends PHPUnit_Framework_TestCase
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