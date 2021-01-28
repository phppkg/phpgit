<?php declare(strict_types=1);

namespace PhpGit\Command;

use PhpGit\Concern\AbstractCommand;
use function gmdate;

/**
 * Class Blame
 *
 * @package PhpGit\Command
 * @refer https://github.com/kzykhys/PHPGit/pull/35/files
 */
class Blame extends AbstractCommand
{
    /**
     * Statuses of the parser
     *
     * These statuses are used by the parser to detect in which section we are
     * of the git-blame output.
     */
    /**
     * Start to parse.
     *
     * @var int
     */
    public const IS_START = 0;

    /**
     * Has collected the hash.
     *
     * @var int
     */
    public const HAS_HASH = 1;

    /**
     * Has collected the name.
     *
     * @var int
     */
    public const HAS_NAME = 2;

    /**
     * Has collected the date.
     *
     * @var int
     */
    public const HAS_DATE = 3;

    /**
     * Define the current file line number that we are trying to blame.
     *
     * @var int
     */
    protected $fileLineNumber = 0;

    /**
     * Define the current position in the git blame output.
     *
     * @var int
     */
    protected $outputIndex = -1;

    /**
     * Counter used to understand where we are in the porcelain block.
     *
     * @var int
     */
    protected $blockCounter = 0;

    /**
     * Contain the git blame output.
     *
     * @var array
     */
    protected $gitOutput = [];

    /**
     * Define the current file line number we are parsing.
     *
     * @var int
     */
    protected $status = self::IS_START;

    /**
     * Blame lines
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     0 => [
     *         'index' => '1',
     *         'hash'  => '1a821f3f8483747fd045eb1f5a31c3cc3063b02b',
     *         'name'  => 'John Doe',
     *         'date'  => 'Fri Jan 17 16:32:49 2014 +0900',
     *         'line'  => '<?php'
     *     ],
     *     1 => [
     *         //...
     *     ]
     * ]
     * ```
     */
    protected $blameLines = [];

    /**
     * @var string|null
     */
    private $currentLine;

    /**
     * @var string|null
     */
    private $nextLine;

    /**
     * Returns the commit logs
     *
     * ``` php
     * $git = new PHPGit\Git();
     * $git->setRepository('/path/to/repo');
     * $lines = $git->blame('/file_to_blame');
     * $lines = $git->blame('/file_to_blame', '1a821f3f8483747fd045eb1f5a31c3cc3063b02b');
     * ```
     *
     * ##### Output Example
     *
     * ``` php
     * [
     *     0 => [
     *         'index' => '1',
     *         'hash'  => '1a821f3f8483747fd045eb1f5a31c3cc3063b02b',
     *         'name'  => 'John Doe',
     *         'date'  => 'Fri Jan 17 16:32:49 2014 +0900',
     *         'line'  => '<?php'
     *     ],
     *     1 => [
     *         //...
     *     ]
     * ]
     * ```
     *
     *
     * @param string      $file The file that we want to blame.
     * @param string|null $hash [optional] The hash of the version of the file that we want to blame.
     *
     * @return array
     */
    public function __invoke(string $file, string $hash = null)
    {
        // $blameLines = array();
        $builder = $this->getCommandBuilder()
            ->add('--line-porcelain')
            ->add($file);

        if ($hash) {
            $builder->add($hash);
        }

        $output = $this->run($builder);
        $lines  = $this->split($output);
        $this->parse($lines);

        return $this->blameLines;
    }

    /**
     * @param array $lines
     */
    public function parse(array $lines): void
    {
        $this->gitOutput = $lines;

        $lineCount = count($lines);
        $blameLine = $this->dispatchBlameArray();
        while ($this->outputIndex <= $lineCount) {
            $this->loadLine();

            if (self::IS_START === $this->status) {
                $blameLine['hash'] = self::extractHash($this->currentLine);

                $this->status = self::HAS_HASH;
                continue;
            }

            if (self::HAS_HASH === $this->status) {
                $blameLine['author'] = self::extractAuthor($this->currentLine);

                $this->status = self::HAS_NAME;
                continue;
            }

            if (self::HAS_NAME === $this->status && $this->blockCounter === 4) {
                $blameLine['date'] = self::extractDate($this->currentLine, $this->nextLine);

                $this->status = self::HAS_DATE;
                continue;
            }

            if (self::HAS_DATE === $this->status && $this->blockCounter === 12) {
                $blameLine['line_content'] = $this->currentLine;

                $this->blameLines[] = $blameLine;
                $blameLine          = $this->dispatchBlameArray();
                continue;
            }
        }
    }

    /**
     * It inits a new array to contain the information for the next putput line.
     * It updates status and counter as single atomic operation.
     *
     * @return array
     */
    public function dispatchBlameArray(): array
    {
        $this->fileLineNumber++;
        $this->blockCounter = 0;

        $this->status = self::IS_START;

        return [
            'line_number'  => $this->fileLineNumber,
            'hash'         => '',
            'author'       => '',
            'date'         => '',
            'line_content' => ''
        ];
    }

    /**
     * It return a specific output line after it checks the line exists.
     *
     * @param $number
     *
     * @return string|null
     */
    public function getLine($number): ?string
    {
        return $this->gitOutput[$number] ?? null;
    }

    /**
     * Load the current line, the next line and
     * It does update the counters time as single atomic operation.
     *
     * @return void
     */
    public function loadLine(): void
    {
        $this->outputIndex++;
        $this->blockCounter++;
        $this->currentLine = $this->getLine($this->outputIndex);

        $this->nextLine = $this->getLine($this->outputIndex + 1);
    }

    /**
     * Extract commit hash from output line.
     *
     * @param string $line The hash raw line
     *
     * @return string
     */
    public static function extractHash(string $line): string
    {
        $line  = trim($line);
        $parts = explode(" ", $line);

        return $parts[0];
    }

    /**
     * Extract author name line from output.
     *
     * @param string $author The author line
     *
     * @return string
     */
    public static function extractAuthor(string $author): string
    {
        $author = str_replace("author", "", $author);

        return trim($author);
    }

    /**
     * Extract date line from output.
     *
     * @param string $timestamp The author-time
     * @param string $timezone  The author-tz
     *
     * @return string
     */
    public static function extractDate(string $timestamp, string $timezone): string
    {
        $timestamp = trim(str_replace("author-time", "", $timestamp));
        $timezone  = trim(str_replace("author-tz", "", $timezone));

        // eg: +1000 is 10 hours
        // 10h * 60 min * 60 sec
        $timezoneSecsDelta = (int)$timezone / 100 * 60 * 60;

        $timestamp += $timezoneSecsDelta;

        return gmdate("Y-m-d H:i:s " . $timezone, $timestamp);
    }
}
