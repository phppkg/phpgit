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
use Symfony\Component\Filesystem\Filesystem;

class CheckoutTest extends BasePhpGitTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', '');
        $git->add('test.txt');
        $git->commit('Initial commit');
    }

    public function testCheckout(): void
    {
        $git = new Git();
        $git->setRepository($this->directory);
        $git->branch->create('next');
        $git->checkout('next');

        $branches = $git->branch();
        $this->assertArrayHasKey('next', $branches);
        $this->assertTrue($branches['next']['current']);
    }

    public function testCheckoutCreate(): void
    {
        $git = new Git();
        $git->setRepository($this->directory);
        $git->checkout->create('next');

        $branches = $git->branch();
        $this->assertArrayHasKey('next', $branches);
        $this->assertTrue($branches['next']['current']);

        $git->checkout->create('develop', 'next');

        $branches = $git->branch();
        $this->assertArrayHasKey('develop', $branches);
        $this->assertTrue($branches['develop']['current']);
    }

    public function testCheckoutOrphan(): void
    {
        $git = new Git();
        $git->setRepository($this->directory);
        $git->checkout->orphan('gh-pages', 'master', ['force' => true]);

        $status = $git->status();
        $this->assertEquals('gh-pages', $status['branch']);
    }
}
