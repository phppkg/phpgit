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

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class ArchiveTest extends BasePhpGitTestCase
{
    public function testArchive(): void
    {
        $filesystem = new Filesystem();
        $filesystem->mkdir($this->directory);

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/test.txt', 'hello');
        $git->add('test.txt');
        $git->commit('Initial commit');

        $git->archive($this->directory . '/test.zip', 'master', null, ['format' => 'zip', 'prefix' => 'test/']);

        $this->assertFileExists($this->directory . '/test.zip');
    }
}
