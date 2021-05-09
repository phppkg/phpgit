<?php declare(strict_types=1);

namespace PhpGit\Changelog;

/**
 * Class LineParserInterface
 * @package PhpGit\Changelog
 */
interface LineParserInterface
{
    /**
     * @param string $line log line string. by git log output
     *
     * @return array returns an item array. {@see GitChangeLog::$logItems}
     */
    public function parse(string $line): array;
}
