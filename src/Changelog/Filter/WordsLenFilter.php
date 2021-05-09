<?php declare(strict_types=1);

namespace PhpGit\Changelog\Filter;

use PhpGit\Changelog\GitChangeLog;
use function str_word_count;
use function trim;

/**
 * Class WordsLenFilter
 * @package PhpGit\Changelog\Filter
 */
final class WordsLenFilter
{
    /**
     * @var int
     */
    protected $minNum;

    /**
     * Class constructor.
     *
     * @param int $minNum
     */
    public function __construct(int $minNum = 2)
    {
        $this->minNum = $minNum;
    }

    /**
     * @param array $item {@see GitChangeLog::LOG_ITEM}
     */
    public function __invoke(array $item): bool
    {
        $msg = trim($item['msg'], '. ');
        $num = str_word_count($msg);

        return $num > $this->minNum;
    }

    /**
     * @param int $minNum
     */
    public function setMinNum(int $minNum): void
    {
        $this->minNum = $minNum;
    }
}
