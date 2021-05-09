<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phpcom-lab/phpgit
 * @license  MIT
 */

use PhpGit\Exception\GitException;
use PhpGit\Git;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class AddTest extends BaseTestCase
{
    public function testAdd(): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir($this->directory);

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', 'foo');
        $filesystem->dumpFile($this->directory . '/test.md', '**foo**');

        $this->assertTrue($git->add('test.txt'));
        $this->assertTrue($git->add(['test.md'], ['force' => true]));
    }

    public function testException(): void
    {
        $this->expectException(GitException::class);
        $this->expectExceptionCode(128);
        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);
        $git->add('foo');
    }
}
