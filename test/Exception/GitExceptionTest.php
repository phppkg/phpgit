<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

use PhpGit\Exception\GitException;
use PhpGit\Git;
use PHPUnit\Framework\TestCase;

class GitExceptionTest extends TestCase
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
