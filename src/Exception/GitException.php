<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/phppkg/phpgit
 * @license  MIT
 */

namespace PhpGit\Exception;

use Exception;
use RuntimeException;

/**
 * @author Kazuyuki Hayashi <hayashi@valnur.net>
 */
class GitException extends RuntimeException
{
    /**
     * @var string
     */
    protected string $commandLine;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @param string         $message     The Exception message to throw.
     * @param int            $code        The Exception code.
     * @param string         $commandLine Command-line string
     * @param Exception|null $previous    The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct(string $message = '', int $code = 0, string $commandLine = '', ?Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->commandLine = $commandLine;
    }

    /**
     * @return string
     */
    public function getCommandLine(): string
    {
        return $this->commandLine;
    }
}
