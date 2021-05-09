<?php declare(strict_types=1);

namespace PhpGit\Changelog\Filter;

use PhpGit\Changelog\GitChangeLog;
use function strlen;

/**
 * Class MsgLenFilter
 * @package PhpGit\Changelog\Filter
 */
final class MsgLenFilter
{
    /**
     * @var int
     */
    protected $minLen;

    /**
     * Class constructor.
     *
     * @param int $minLen
     */
    public function __construct(int $minLen = 10)
    {
        $this->minLen = $minLen;
    }

    /**
     * @param array $item {@see GitChangeLog::LOG_ITEM}
     */
    public function __invoke(array $item): bool
    {
        return strlen($item['msg']) > $this->minLen;
    }

    /**
     * @param int $minLen
     */
    public function setMinLen(int $minLen): void
    {
        $this->minLen = $minLen;
    }
}
