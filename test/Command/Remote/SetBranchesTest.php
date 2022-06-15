<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGitTest\Command\Remote;

use PhpGit\Git;
use PhpGitTest\BasePhpGitTestCase;

class SetBranchesTest extends BasePhpGitTestCase
{
    public function testSetBranches(): void
    {
        $git = new Git();
        $git->clone('https://github.com/phppkg/phpgit.git', $this->directory);
        $git->setRepository($this->directory);

        $git->remote->branches('origin', ['master']);
    }

    public function testSetBranchesAdd(): void
    {
        $git = new Git();
        $git->clone('https://github.com/phppkg/phpgit.git', $this->directory);
        $git->setRepository($this->directory);

        $git->remote->branches->add('origin', ['gh-pages']);
    }
}
