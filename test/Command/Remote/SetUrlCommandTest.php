<?php declare(strict_types=1);
/**
 * phpgit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
 * @license  MIT
 */

use PhpGit\Git;

require_once __DIR__ . '/../../BaseTestCase.php';

class SetUrlCommandTest extends BaseTestCase
{
    public function testSetUrl(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url('origin', 'https://github.com/kzykhys/Text.git', 'http://example.com/test.git');

        $remotes = $git->remote();

        $this->assertEquals('https://github.com/kzykhys/Text.git', $remotes['origin']['fetch']);
    }

    public function testSetUrlAdd(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url->add('origin', 'https://github.com/kzykhys/Text.git');
    }

    public function testSetUrlDelete(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url->add('origin', 'https://github.com/kzykhys/Text.git');
        $git->remote->url->delete('origin', 'https://github.com');

        $remotes = $git->remote();

        $this->assertEquals('http://example.com/test.git', $remotes['origin']['fetch']);
    }
}
