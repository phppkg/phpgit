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
     * Class constructor.
     *
     * @param array $keywords
     */
    public function __construct(array $keywords)
    {
        $this->keywords = $keywords;
    }

    /**
     * @param array $item {@see GitChangeLog::LOG_ITEM}
     */
    public function __invoke(array $item): bool
    {
        $msg = $item['msg'];
        foreach ($this->keywords as $keyword) {
            if (stripos($msg, $keyword) !== false) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string[] $keywords
     */
    public function setKeywords(array $keywords): void
    {
        $this->keywords = $keywords;
    }
}
