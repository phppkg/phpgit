<?php declare(strict_types=1);

namespace PhpGit\Changelog;

use PhpGit\Changelog\Formatter\MarkdownFormatter;
use InvalidArgumentException;
use RuntimeException;
use function array_merge;
use function count;
use function implode;
use function is_array;
use function is_callable;
use function is_object;
use function is_string;
use function strpos;
use function trim;

/**
 * Class GitChangeLog
 *
 * @package Inhere\Kite\Model\Logic
 */
class GitChangeLog
{
    public const SEP = ' | ';

    public const OTHER_GROUP = 'Other';

    // see https://devhints.io/git-log-format
    // see https://git-scm.com/docs/pretty-formats
    // - %n   new line
    // id, msg
    public const LOG_FMT_HS = '%H | %s';
    // public const LOG_FMT_HS1 = 'id=%h msg=%s';
    // id, msg, author
    public const LOG_FMT_HSA = '%H | %s | %an';
    // id, msg, author date
    public const LOG_FMT_HSD = '%H | %s | %ai';
    // id, msg, committer
    public const LOG_FMT_HSC = '%H | %s | %cn';
    // id, msg, commit date
    public const LOG_FMT_HSD1 = '%H | %s | %ci';

    public const LOG_ITEM = [
        'hashId'    => '', // %H %h
        'parentId'  => '', // %P %p
        'msg'       => '', // %s
        'date'      => '',  // %ci
        'author'    => '',  // %an
        'committer' => '',  // %cn
    ];

    /**
     * @var bool
     */
    protected $parsed = false;

    /**
     * Not output group name line.
     *
     * group - The log group name. eg: Update, Fix, Feature.
     *         If is empty or other, will use group Other
     *
     * @var bool
     */
    protected $noGroup = false;

    /**
     * Title string for formatted text. eg: ## Change Log
     *
     * @var string
     */
    protected $title = "## Change Log";

    /**
     * @var string
     */
    protected $repoUrl = '';

    /**
     * @var string
     */
    protected $logOutput = '';

    /**
     * built-in log format string on the `git log --pretty="format:%H"`.
     * see self::LOG_FMT_*
     *
     * @var string
     */
    protected $logFormat = self::LOG_FMT_HS;

    /**
     * @var callable|LineParserInterface
     */
    protected $lineParser;

    /**
     * The item formatter. format each item to string
     *
     * @var callable|ItemFormatterInterface
     */
    protected $itemFormatter;

    /**
     * parsed log items
     *
     * item:
     * [
     * id => string,
     * msg => string,
     * date => string,
     * author => string,
     * committer => string,
     * ]
     *
     * @var array
     */
    protected $logItems = [];

    /**
     * formatted lines by $itemFormatter
     *
     * @var array
     */
    protected $formatted = [];

    /**
     * @return static
     */
    public static function new(string $logOutput = ''): self
    {
        return new self($logOutput);
    }

    /**
     * Class constructor.
     *
     * @param string $logOutput
     */
    public function __construct(string $logOutput = '')
    {
        $this->logOutput = $logOutput;
    }

    /**
     * @param string $output
     */
    public function load(string $output): void
    {
        $this->logOutput = trim($output);
    }

    /**
     * @return $this
     */
    public function parse(): self
    {
        if ($this->parsed) {
            return $this;
        }
        $this->parsed = true;

        // see https://devhints.io/git-log-format

        /**
         * git log output
         * - each line like: `commitID message`
         */
        $str = trim($this->logOutput);
        if (!$str) {
            throw new RuntimeException('please load git log output for parse');
        }

        $isParser = false;
        if ($parser = $this->lineParser) {
            $isParser = is_object($parser) && $parser instanceof LineParserInterface;
        }

        foreach (explode("\n", $str) as $line) {
            if (!$line = trim($line)) {
                continue;
            }

            // fix: symfony process's output will quote `"`
            if (!$line = trim($line, '"\'')) {
                continue;
            }

            if ($parser) {
                if ($isParser) {
                    $item = $parser->parse($line);
                } else {
                    $item = $parser($line);
                }
            } else {
                $item = $this->builtInParse($line);
            }

            // merge for ensure field exists
            $this->logItems[] = array_merge(self::LOG_ITEM, $item);
        }

        return $this;
    }

    /**
     * @param string $line
     *
     * @return array
     */
    protected function builtInParse(string $line): array
    {
        $an = $cn = $date = '';
        switch ($this->logFormat) {
            case self::LOG_FMT_HS:
                [$id, $msg] = explode(self::SEP, $line, 2);
                break;
            case self::LOG_FMT_HSA:
                [$id, $msg, $an] = explode(self::SEP, $line, 3);
                break;
            case self::LOG_FMT_HSC:
                [$id, $msg, $cn] = explode(self::SEP, $line, 3);
                break;
            case self::LOG_FMT_HSD:
            case self::LOG_FMT_HSD1:
                [$id, $msg, $date] = explode(self::SEP, $line, 3);
                break;
            default:
                throw new RuntimeException('not supported log format or not set lineParser');
        }

        return [
            'hashId'    => $id,
            // 'parentId'    => $pid,
            'msg'       => $msg,
            'date'      => $date,
            'author'    => $an,
            'committer' => $cn,
        ];
    }

    /**
     * @return string
     */
    public function format(): string
    {
        // parse
        $this->parse();

        // do format parsed
        $groupNames = $this->doFormat();

        $outLines = [];
        $groupNum = count($groupNames);

        // first add title
        if ($this->title) {
            $outLines[] = $this->title;
        }

        // build string
        foreach ($this->formatted as $group => $lines) {
            // only one group, not render group name.
            if ($groupNum > 1) {
                $outLines[] = $group;
            }

            $outLines[] = implode("\n", $lines);
        }

        return implode("\n", $outLines);
    }

    /**
     * @return array
     */
    protected function doFormat(): array
    {
        $formatter = $this->itemFormatter;
        if (!$formatter) {
            $formatter = new MarkdownFormatter();
        }

        $groupNames  = [];
        $isFormatter = is_object($formatter) && $formatter instanceof ItemFormatterInterface;

        $otherGroup = '';
        foreach ($this->logItems as $item) {
            $item['url'] = $this->repoUrl;

            if ($isFormatter) {
                $formatted = $formatter->format($item);
            } else {
                $formatted = $formatter($item);
            }

            if (!$formatted) {
                continue;
            }

            $group = self::OTHER_GROUP;
            if (is_string($formatted)) {
                $line = $formatted;
            } elseif (is_array($formatted)) {
                if (count($formatted) > 1) {
                    $group = $formatted[0] ?: self::OTHER_GROUP;
                    $line  = $formatted[1];
                } else {
                    $line = $formatted[0];
                }
            } else { // invalid return.
                continue;
            }

            // if returned group name add suffix or prefix
            if (!$otherGroup && strpos($group, self::OTHER_GROUP) !== false) {
                $otherGroup = $group;
            }

            $groupNames[$group] = $group;
            // add line
            $this->formatted[$group][] = $line;
        }

        // up: keep the other group on last.
        if (isset($this->formatted[$otherGroup]) && count($groupNames) > 1) {
            $groupLines = $this->formatted[$otherGroup];
            unset($this->formatted[$otherGroup]);

            // re-add on last
            $this->formatted[$otherGroup] = $groupLines;
        }

        return $groupNames;
    }

    /**
     * @return array
     */
    public function getChangeLog(): array
    {
        return $this->logItems;
    }

    /**
     * @return array
     */
    public function getLogItems(): array
    {
        return $this->logItems;
    }

    /**
     * @return string
     */
    public function getLogFormat(): string
    {
        return $this->logFormat;
    }

    /**
     * @param string $logFormat
     */
    public function setLogFormat(string $logFormat): void
    {
        $this->logFormat = $logFormat;
    }

    /**
     * @param callable|ItemFormatterInterface $itemFormatter
     */
    public function setItemFormatter($itemFormatter): void
    {
        if (is_object($itemFormatter) && $itemFormatter instanceof ItemFormatterInterface) {
            $this->itemFormatter = $itemFormatter;
        } elseif (is_callable($itemFormatter)) {
            $this->itemFormatter = $itemFormatter;
        } else {
            throw new InvalidArgumentException('set invalid item formatter');
        }
    }

    /**
     * @param callable|LineParserInterface $lineParser
     */
    public function setLineParser($lineParser): void
    {
        if (is_object($lineParser) && $lineParser instanceof LineParserInterface) {
            $this->lineParser = $lineParser;
        } elseif (is_callable($lineParser)) {
            $this->lineParser = $lineParser;
        } else {
            throw new InvalidArgumentException('set invalid item formatter');
        }
    }

    /**
     * @return string
     */
    public function getLogOutput(): string
    {
        return $this->logOutput;
    }

    /**
     * @param string $logOutput
     */
    public function setLogOutput(string $logOutput): void
    {
        $this->logOutput = $logOutput;
    }

    /**
     * @param bool $noGroup
     */
    public function setNoGroup(bool $noGroup): void
    {
        $this->noGroup = $noGroup;
    }

    /**
     * @return string
     */
    public function getRepoUrl(): string
    {
        return $this->repoUrl;
    }

    /**
     * @param string $repoUrl
     */
    public function setRepoUrl(string $repoUrl): void
    {
        $this->repoUrl = $repoUrl;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
}
