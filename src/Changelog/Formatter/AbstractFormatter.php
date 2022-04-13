<?php declare(strict_types=1);

namespace PhpGit\Changelog\Formatter;

use PhpGit\Changelog\ChangeLogUtil;
use PhpGit\Changelog\GitChangeLog;
use PhpGit\Changelog\ItemFormatterInterface;
use function array_merge;

/**
 * Class AbstractFormatter
 *
 * @package PhpGit\Changelog\Formatter
 */
abstract class AbstractFormatter implements ItemFormatterInterface
{
    /**
     * The group name match rules. key is the group name.
     *
     * @var array<string, array<string, string[]>>
     * @see defaultRules()
     */
    protected array $rules = [];

    /**
     * Class constructor.
     *
     * @param array $rules
     */
    public function __construct(array $rules = [])
    {
        if ($rules) {
            $this->rules = array_merge(self::defaultRules(), $rules);
        } else {
            $this->rules = self::defaultRules();
        }
    }

    public static function defaultRules(): array
    {
        return [
            'Refactor' => [
                'startWiths' => ['break', 'refactor'],
                'contains'   => [],
            ],
            'Update'   => [
                'startWiths' => ['up', 'add', 'create', 'prof', 'perf', 'enhance'],
                'contains'   => [],
            ],
            'Feature'  => [
                'startWiths' => ['feat', 'support', 'new'],
                'contains'   => [],
            ],
            'Fixed'    => [
                'startWiths' => ['bug', 'fix', 'close'],
                'contains'   => [' fix']
            ]
        ];
    }

    /**
     * @param string $msg
     *
     * @return string
     */
    public function matchGroup(string $msg): string
    {
        foreach ($this->rules as $group => $rule) {
            if (ChangeLogUtil::matchMsgByRule($msg, $rule)) {
                return $group;
            }
        }

        return GitChangeLog::OTHER_GROUP;
    }

    /**
     * @param array $item each line item {@see GitChangeLog::LOG_ITEM}
     *
     * @return string[] returns [group, line string]
     */
    abstract public function format(array $item): array;

    /**
     * @return array[]
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @param array[] $rules
     */
    public function setRules(array $rules): void
    {
        $this->rules = $rules;
    }
}
