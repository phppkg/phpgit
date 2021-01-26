<?php declare(strict_types=1);

namespace PhpGit;

use PhpGit\Command\Remote;

/**
 * Class Repo
 *
 * @package PhpGit
 */
class Repo
{
    /**
     * @var Git
     */
    private $git;

    /**
     * @var string
     */
    private $repoDir;

    /**
     * @param string $repoDir
     *
     * @return static
     */
    public static function new(string $repoDir): self
    {
        return new self($repoDir);
    }

    /**
     * @param Git $git
     *
     * @return static
     */
    public static function newByGit(Git $git): self
    {
        $self = new self($git->getDirectory());
        $self->setGit($git);

        return $self;
    }

    public function __construct(string $repoDir)
    {
        $this->repoDir = $repoDir;
    }

    public function getRemote(string $name): Remote
    {

    }

    public function getRemotes(): array
    {

    }

    /**
     * @return Git
     */
    private function ensureGit(): Git
    {
        if (!$this->git) {
            $this->git = new Git($this->repoDir);
        }

        return $this->git;
    }

    /**
     * @return string
     */
    public function getRepoDir(): string
    {
        return $this->repoDir;
    }

    /**
     * @param Git $git
     *
     * @return Repo
     */
    public function setGit(Git $git): Repo
    {
        $this->git = $git;
        return $this;
    }

    /**
     * @return Git
     */
    public function getGit(): Git
    {
        return $this->git;
    }
}
