<?php declare(strict_types=1);

namespace PhpGit\Changelog;

use PhpGit\Git;
use function is_dir;
use function is_writable;
use function mkdir;
use function stripos;
use function strtolower;

/**
 * Class ChangeLogUtil
 * @package PhpGit\Changelog
 */
class ChangeLogUtil
{
    /**
     * @param string $msg
     * @param array{startWiths: list<string>, contains: list<string>} $rule
     *
     * @return bool
     */
    public static function matchMsgByRule(string $msg, array $rule): bool
    {
        if (isset($rule['startWiths'])) {
            foreach ($rule['startWiths'] as $start) {
                if (stripos($msg, $start) === 0) {
                    return true;
                }
            }
        }

        if (isset($rule['contains'])) {
            foreach ($rule['contains'] as $sub) {
                if (stripos($msg, $sub) > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param string $path
     * @param int    $mode
     * @param bool   $recursive
     *
     * @return bool
     */
    public static function mkdir(string $path, int $mode = 0775, bool $recursive = true): bool
    {
        return (is_dir($path) || !(!@mkdir($path, $mode, $recursive) && !is_dir($path))) && is_writable($path);
    }

    /**
     * @param string $version
     *
     * @return string
     */
    public static function getVersion(string $version): string
    {
        $toLower = strtolower($version);
        if ($toLower === 'head') {
            return 'HEAD';
        }

        $descSortedTags = Git::new()->tag->tagsInfo('-version:refname');

        if ($toLower === 'latest' || $toLower === 'last') {
            $version = $descSortedTags->first();
        } elseif ($toLower === 'prev' || $toLower === 'previous') {
            $version = $descSortedTags->second();
        }

        return $version;
    }

}
