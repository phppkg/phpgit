<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

use PhpGit\Git;

require_once __DIR__ . '/../BaseTestCase.php';

class PullCommandTest extends BaseTestCase
{
    public function testPull(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $git->remote->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->pull('origin', 'master');

        $this->assertFileExists($this->directory . '/README.md');
    }
}
