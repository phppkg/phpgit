<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

use PhpGit\Git;

require_once __DIR__ . '/../../BaseTestCase.php';

class SetUrlTest extends BaseTestCase
{
    public function testSetUrl(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url('origin', 'https://github.com/phpcom-lab/phpgit.git', 'http://example.com/test.git');

        $remotes = $git->remote();

        $this->assertEquals('https://github.com/phpcom-lab/phpgit.git', $remotes['origin']['fetch']);
    }

    public function testSetUrlAdd(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
    }

    public function testSetUrlDelete(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url->add('origin', 'https://github.com/phpcom-lab/phpgit.git');
        $git->remote->url->delete('origin', 'https://github.com');

        $remotes = $git->remote();

        $this->assertEquals('http://example.com/test.git', $remotes['origin']['fetch']);
    }
}
