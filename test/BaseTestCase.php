<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

// namespace PhpGitTest;

use PhpGit\Git;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author Kazuyuki Hayashi <hayashi@siance.co.jp>
 */
abstract class BaseTestCase extends TestCase
{
    /**
     * @var string
     */
    protected $directory;

    public static function setUpBeforeClass(): void
    {
        $git = Git::new();
        $opt = [
            'global' => true,
        ];

        //  git config --global user.email "you@example.com"
        //  git config --global user.name "Your Name"
        if (!$git->config->get('user.name', $opt)) {
            $git->config->set('user.name', 'inhere', $opt);
            $git->config->set('user.email', 'in.798@qq.com', $opt);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->directory = __DIR__ . '/testdata/build/' . strtolower(get_class($this));
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->directory);
    }
}
