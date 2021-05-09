<?php declare(strict_types=1);

namespace PhpGit\Changelog\Formatter;

use function sprintf;

/**
 * Class GithubReleaseFormatter
 * @package PhpGit\Changelog\Formatter
 */
class GithubReleaseFormatter extends MarkdownFormatter
{
    /**
     * @param array $item
     *
     * @return string[]
     */
    public function format(array $item): array
    {
        $msg = $item['msg'];
        $url = $item['url'];
        $grp = $this->matchGroup($msg);

        $line = sprintf(' - %s', $msg);
        if ($hid = $item['hashId']) {
            // eg: https://github.com/inhere/kite/commit/ebd90a304755218726df4eb398fd081c08d04b9a
            $line .= sprintf(' %s/commit/%s', $url, $hid);
        }

        return [$grp, $line];
    }
}
