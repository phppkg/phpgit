<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

use PhpGit\Git;

require_once __DIR__ . '/../../BaseTestCase.php';

class SetHeadCommandTest extends BaseTestCase
{
    public function testSetHead(): void
    {
        $git = new Git();
        $git->clone('https://github.com/kzykhys/Text.git', $this->directory);
        $git->setRepository($this->directory);

        $before = $git->branch(['all' => true]);

        $git->remote->head('origin', 'master');

        $after = $git->branch(['all' => true]);

        $this->assertEquals($before, $after);
    }

    public function testSetHeadDelete(): void
    {
        $git = new Git();
        $git->clone('https://github.com/kzykhys/Text.git', $this->directory);
        $git->setRepository($this->directory);

        $before = $git->branch(['all' => true]);

        $git->remote->head->delete('origin');

        $after = $git->branch(['all' => true]);

        $this->assertNotEquals($before, $after);
    }

    public function testSetHeadRemote(): void
    {
        $git = new Git();
        $git->clone('https://github.com/kzykhys/Text.git', $this->directory);
        $git->setRepository($this->directory);

        $before = $git->branch(['all' => true]);

        $git->remote->head->delete('origin');
        $git->remote->head->remote('origin');

        $after = $git->branch(['all' => true]);

        $this->assertEquals($before, $after);
    }
}
