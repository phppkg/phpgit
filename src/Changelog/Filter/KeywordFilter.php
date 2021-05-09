<?php declare(strict_types=1);

namespace PhpGit\Changelog\Filter;

use PhpGit\Changelog\GitChangeLog;
use function stripos;
use function strlen;

/**
 * Class KeywordFilter
 * @package PhpGit\Changelog\Filter
 */
final class KeywordFilter
{
    /**
     * @var string
     */
    private $keyword;

    /**
     * Class constructor.
     *
     * @param string $keyword
     */
    public function __construct(string $keyword)
    {
        $this->keyword = $keyword;
    }

    /**
     * @param array $item {@see GitChangeLog::LOG_ITEM}
     */
    public function __invoke(array $item): bool
    {
        $msg = $item['msg'];

        return stripos($msg, $this->keyword) !== false;
    }

    /**
     * @param string $keyword
     */
    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }
}
