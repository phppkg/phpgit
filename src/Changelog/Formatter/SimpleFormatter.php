<?php declare(strict_types=1);

namespace PhpGit\Changelog\Formatter;

use function sprintf;
use function strpos;
use function substr;

/**
 * Class SimpleFormatter
 * @package PhpGit\Changelog\Formatter
 */
class SimpleFormatter extends AbstractFormatter
{
    /**
     * @param array $item
     *
     * @return string[]
     */
    public function format(array $item): array
    {
        $line = ' - ';
        if ($hid = $item['hashId']) {
            $abbrev7 = substr($hid, 0, 7);
            $line .= $abbrev7 . ' ';
        }

        $msg  = $item['msg'];
        $grp = $this->matchGroup($msg);

        $line .= $msg;
        $user = $item['author'] ?: $item['committer'];
        if ($user) {
            $line .= " (by $user)";
        }

        return [$grp, $line];
    }
}
