<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

use PhpGit\Git;
use Symfony\Component\Filesystem\Filesystem;



class RmTest extends BaseTestCase
{
    public function testRm(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $filesystem->dumpFile($this->directory . '/bin/test.php', 'foo');
        $git->add(['README.md', 'bin/test.php']);
        $git->commit('Initial commit');

        $git->rm('README.md');
        $git->rm('bin', ['recursive' => true]);

        $this->assertFileNotExists($this->directory . '/README.md');
    }

    public function testRmCached(): void
    {
        $filesystem = new Filesystem();

        $git = new Git();
        $git->init($this->directory);
        $git->setRepository($this->directory);

        $filesystem->dumpFile($this->directory . '/README.md', 'foo');
        $git->add('README.md');
        $git->commit('Initial commit');

        $git->rm->cached('README.md');
        $git->commit('Delete README.md');

        $this->assertFileExists($this->directory . '/README.md');

        $tree = $git->tree();
        $this->assertEquals([], $tree);
    }
}
