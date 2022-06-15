<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGitTest\Info;

use PhpGit\Exception\GitException;
use PhpGit\Git;
use PHPUnit\Framework\TestCase;

/**
 * class GitExceptionTest
 *
 * @author inhere
 * @date 2022/6/15
 */
class GitExceptionTest extends TestCase
{
    public function testGitException(): void
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
