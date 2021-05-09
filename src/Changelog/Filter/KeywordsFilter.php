<?php declare(strict_types=1);

namespace PhpGit\Changelog\Filter;

use PhpGit\Changelog\GitChangeLog;
use function stripos;

/**
 * Class KeywordsFilter
 * @package PhpGit\Changelog\Filter
 */
final class KeywordsFilter
{
    /**
     * @var string[]
     */
    private $keywords;
    /**
     * @var bool
     */
    private $exclude;

    /**
     * Class constructor.
     *
     * @param array $keywords
     * @param bool  $exclude
     */
    public function __construct(array $keywords, bool $exclude = true)
    {
        $this->keywords = $keywords;
        $this->exclude  = $exclude;
    }

    /**
     * @param array $item {@see GitChangeLog::LOG_ITEM}
     */
    public function __invoke(array $item): bool
    {
        $msg = $item['msg'];

        foreach ($this->keywords as $keyword) {
            if (stripos($msg, $keyword) !== false) {
                return !$this->exclude;
            }
        }

        return $this->exclude;
    }

    /**
     * @param string[] $keywords
     */
    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
    }
}
