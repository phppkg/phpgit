<?php declare(strict_types=1);

namespace PhpGit\Changelog\Filter;

use PhpGit\Changelog\GitChangeLog;
use function stripos;

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
     * @var bool
     */
    private $exclude;

    /**
     * Class constructor.
     *
     * @param string $keyword
     * @param bool   $exclude
     */
    public function __construct(string $keyword, bool $exclude = true)
    {
        $this->keyword = $keyword;
        $this->exclude = $exclude;
    }

    /**
     * @param array $item {@see GitChangeLog::LOG_ITEM}
     */
    public function __invoke(array $item): bool
    {
        $msg = $item['msg'];
        $has = stripos($msg, $this->keyword) !== false;

        if ($this->exclude) {
            return !$has;
        }

        return $has;
    }

    /**
     * @param string $keyword
     */
    public function setKeyword(string $keyword): void
    {
        $this->keyword = $keyword;
    }
}
