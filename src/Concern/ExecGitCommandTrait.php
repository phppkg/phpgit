<?php declare(strict_types=1);

namespace PhpGit\Concern;

use BadMethodCallException;
use PhpGit\Git;
use RuntimeException;

/**
 * Trait ExecGitCommandTrait
 *
 * @package PhpGit\Concern
 */
trait ExecGitCommandTrait
{
    /**
     * Loaded git command object. key is command name.
     *
     * @var AbstractCommand[]
     */
    private $commands = [];

    /**
     * @return Git
     */
    abstract public function getGit(): Git;

    /**
     * @param string $name
     * @param mixed  ...$args
     *
     * @return string
     */
    public function exec(string $name, ...$args): string
    {
        if (isset(self::COMMANDS[$name])) {
            // lazy load command
            $cmd = $this->initCommand($name);

            // has __invoke() method
            if (is_callable($cmd)) {
                return $cmd(...$args);
            }
        }

        return $this->getGit()->newCmd($name, ...$args)->run();
        // throw new BadMethodCallException(sprintf('Call to undefined git command: %s', $name));
    }

    /**
     * @param string $name
     *
     * @return AbstractCommand
     */
    protected function initCommand(string $name): AbstractCommand
    {
        // lazy load command
        if (!isset($this->commands[$name])) {
            $class = self::COMMANDS[$name];
            // save
            $this->commands[$name] = new $class($this->getGit());
        }

        return $this->commands[$name];
    }

    /**
     * Quick calls sub-commands
     *
     * @param string $name      The name of a property
     * @param array  $args An array of arguments
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call(string $name, array $args)
    {
        return $this->exec($name, ...$args);
    }

    /**
     * @param string $name
     *
     * @return AbstractCommand
     */
    public function __get(string $name)
    {
        // lazy load command
        if (isset(self::COMMANDS[$name])) {
            return $this->initCommand($name);
        }

        throw new BadMethodCallException(sprintf('Access an undefined property PhpGit\Git->%s', $name));
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function __set(string $name, $value): void
    {
        throw new RuntimeException('unsupported set the property ' . $name);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function __isset(string $name)
    {
        return isset(self::COMMANDS[$name]);
    }
}
