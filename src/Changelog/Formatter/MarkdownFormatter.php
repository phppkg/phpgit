<?php declare(strict_types=1);

namespace PhpGit\Changelog\Formatter;

use function sprintf;
use function substr;

/**
 * Class MarkdownFormatter
 * @package PhpGit\Changelog\Formatter
 */
class MarkdownFormatter extends AbstractFormatter
{
    /**
     * @param string $msg
     *
     * @return string
     */
    public function matchGroup(string $msg): string
    {
        return "\n### " . parent::matchGroup($msg) . "\n";
    }

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
            $abbrev7 = substr($hid, 0, 7);

            // eg: https://github.com/inhere/kite/commit/ebd90a304755218726df4eb398fd081c08d04b9a
            $line .= sprintf(' [%s](%s/commit/%s)', $abbrev7, $url, $hid);
        }

        $user = $item['author'] ?: $item['committer'];
        if ($user) {
            $line .= " (by $user)";
        }

        return [$grp, $line];
    }
}
