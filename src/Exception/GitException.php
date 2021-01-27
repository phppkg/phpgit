<?php declare(strict_types=1);
/**
 * phpGit - A Git wrapper for PHP
 *
 * @author   https://github.com/inhere
 * @link     https://github.com/ulue/phpgit
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
    protected $commandLine;

    /**
     * Construct the exception. Note: The message is NOT binary safe.
     *
     * @param string         $message     [optional] The Exception message to throw.
     * @param int            $code        [optional] The Exception code.
     * @param null           $commandLine [optional] Command-line
     * @param Exception|null $previous    [optional] The previous exception used for the exception chaining. Since 5.3.0
     */
    public function __construct($message = '', $code = 0, $commandLine = null, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->commandLine = $commandLine;
    }

    /**
     * @return null|string
     */
    public function getCommandLine(): ?string
    {
        return $this->commandLine;
    }
}