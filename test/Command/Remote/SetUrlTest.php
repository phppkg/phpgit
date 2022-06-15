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

/**
 * class SetUrlTest
 *
 * @author inhere
 * @date 2022/6/15
 */
class SetUrlTest extends BasePhpGitTestCase
{
    public function testSetUrl(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url('origin', 'https://github.com/phppkg/phpgit.git', 'http://example.com/test.git');

        $remotes = $git->remote();

        $this->assertEquals('https://github.com/phppkg/phpgit.git', $remotes['origin']['fetch']);
    }

    public function testSetUrlAdd(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url->add('origin', 'https://github.com/phppkg/phpgit.git');
    }

    public function testSetUrlDelete(): void
    {
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->remote->add('origin', 'http://example.com/test.git');
        $git->remote->url->add('origin', 'https://github.com/phppkg/phpgit.git');
        $git->remote->url->delete('origin', 'https://github.com');

        $remotes = $git->remote();

        $this->assertEquals('http://example.com/test.git', $remotes['origin']['fetch']);
    }
}
