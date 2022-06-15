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

class PullTest extends BasePhpGitTestCase
{
    public function testPull(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $git->remote->add('origin', 'https://github.com/phppkg/phpgit.git');
        $git->pull('origin', 'master');

        $this->assertFileExists($this->directory . '/README.md');
    }
}
