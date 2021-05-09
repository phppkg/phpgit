<?php declare(strict_types=1);

namespace PhpGit\Changelog;

/**
 * Class ItemFormatterInterface
 * @package PhpGit\Changelog
 */
interface ItemFormatterInterface
{
    /**
     * @param string $msg
     *
     * @return string
     */
    public function matchGroup(string $msg): string;

    /**
     * @param array $item each line item {@see GitChangeLog::LOG_ITEM}
     *
     * @return string[] returns [group, line string]
     *                  - group  The log group name. eg: Update, Fix, Feature.
     *                           If is empty or other, will use group Other
     *                  - line   The changelog line string.
     */
    public function format(array $item): array;
}
