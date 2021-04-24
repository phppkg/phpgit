<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

use PhpGit\Git;

class FetchTest extends BaseTestCase
{
    public function testFetch(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $git->remote->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
        $git->fetch('origin', '+refs/heads/*:refs/remotes/origin/*');

        $tags = $git->tag();
        $this->assertContains('v1.0.0', $tags);
    }

    public function testFetchAll(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $git->remote->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
        $git->fetch->all();

        $tags = $git->tag();
        $this->assertContains('v1.0.0', $tags);
    }
}
