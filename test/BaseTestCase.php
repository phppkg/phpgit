<?php

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

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->directory = __DIR__ . '/../../build/' . strtolower(get_class($this));
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