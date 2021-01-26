<?php

use PhpGit\Exception\GitException;
use PhpGit\Git;

class GitExceptionTest extends PHPUnit_Framework_TestCase
{

    public function testException(): void
    {
        $git = new Git();
        $git->setRepository(sys_get_temp_dir());
        try {
            $git->status();
            $this->fail('Previous operation should fail');
        } catch (GitException $e) {
            $command = $e->getCommandLine();
            $command = str_replace(['"', "'"], '', $command);
            $this->assertStringEndsWith('status --porcelain -s -b --null', $command);
        }
    }

}